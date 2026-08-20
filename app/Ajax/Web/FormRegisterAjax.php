<?php
namespace FormRegister\Ajax\Web;

use SkillDo\Cms\Support\Option;
use SkillDo\Cache\Cache;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Support\Mail;
use SkillDo\Cms\Support\Url;
use SkillDo\Http\Request;
use Illuminate\Support\Str;
use SkillDo\Support\Path;
use SkillDo\Validate\Rule;

class FormRegisterAjax
{
    static function register(Request $request): void
    {
        $form_key = $request->input('form_key');

        $form 	  = \FormRegister\Models\FormRegister::where('key', $form_key)->first();

        $post 	  = $request->input();

        unset($post['action']);

        /*
        | Thoát sớm và NÓI RÕ lý do.
        |
        | Bản cũ bọc toàn bộ thân hàm trong ba tầng if mà không nhánh nào có else:
        | sai form_key, form không có trường, hay insert() trả SKD_Error đều kết thúc
        | hàm mà không gửi response nào — nút Gửi quay mãi, không log, không console.
        | Nhánh cuối tệ nhất: dữ liệu khách MẤT mà không ai biết.
        */
        if(noItems($form))
        {
            response()->error(trans('generate-form-register::register.gfr.form.notFound', ['key' => html_escape((string) $form_key)]));
        }

        if(noItems($post))
        {
            response()->error(trans('generate-form-register::register.gfr.fail'));
        }

        if($form->is_live == 0)
        {
            response()->error(trans('generate-form-register::register.gfr.form.off'));
        }

        $fields = unserialize($form->field);

        if(noItems($fields))
        {
            response()->error(trans('generate-form-register::register.gfr.form.empty', ['key' => html_escape((string) $form_key)]));
        }

        $errors = apply_filters('generate_form_register_'.$form_key.'_error', '', $form );

        if(is_skd_error($errors))
        {
            response()->error($errors);
        }

        $data 		= [];

        $metadata 	= [];

        $validations = [];

        $mailsData = [];

        foreach ($fields as $inputTypes)
        {
            foreach ($inputTypes as $input) {

                if (isset($input['use']) && $input['use'] == 0) continue;

                $validation = Rule::make($input['label']);

                if (!empty($input['required'])) {
                    $validation->notEmpty();
                }

                if (!empty($input['isEmail'])) {
                    $validation->email();
                }

                if (!empty($input['isPhone'])) {
                    $validation->phone();
                }

                if (!empty($validation->validators())) {
                    $validations[$input['field']] = $validation;
                }
            }
        }

        if(!empty($validations))
        {
            $validate = $request->validate($validations)->validate();

            if ($validate->fails()) {
                response()->error($validate->errors());
            }
        }

        //WD-29: form tạo bằng script/SQL chỉ khai một trong hai nhóm -> khoá vắng mặt là fatal,
        //phía người dùng chỉ thấy "bấm Gửi không có gì xảy ra".
        foreach (($fields['default'] ?? []) as $column => $input)
        {
            $data[$column] = $request->input($input['field']);

            if(is_string($data[$column])) {
                $data[$column] = Str::clear($data[$column]);
            }

            if(!empty($input['use'])) {
                $mailsData[$input['field']] = $data[$column];
            }
        }

        foreach (($fields['metadata'] ?? []) as $column => $input)
        {
            $metadata[$input['name']] = $request->input($input['field']);

            if(is_string($metadata[$input['name']])) {
                $metadata[$input['name']] = Str::clear($metadata[$input['name']]);
            }

            $mailsData[$input['field']] = $metadata[$input['name']];
        }

        $data['status'] 	= 1;

        $data['form_key'] = $form->key;

        $data = apply_filters('generate_form_register_'.$form_key.'_data', $data, $form);

        $res  = \FormRegister\Models\FormRegisterResult::insert($data);

        if(is_skd_error($res))
        {
            //Dữ liệu khách không lưu được -> phải báo, đừng im lặng nuốt mất.
            response()->error($res);
        }

        Cache::delete('generate_form_count_'.$form->key);

        if(hasItems($metadata))
        {
            foreach ($metadata as $meta_key => $meta_value)
            {
                \FormRegister\Models\FormRegisterResult::updateMeta($res, $meta_key, $meta_value);
            }
        }

        if($form->send_email == 1)
        {
            $subject = 'Có email được gửi từ form '.$form->name.' vào '.date('d/m/Y H:i');

            $name = (isset($data['name'])) ? $data['name'] : 'No name';

            $content = file_get_contents(Path::plugin('generate-form-register').'/views/email/template-1.php');

            $content = str_replace('{{email_template}}', $form->email_template, $content);

            $data['base_url'] = Url::base();

            $mailsData = apply_filters('generate_form_register_'.$form_key.'_email_data', $mailsData, $form);

            Mail::to(Option::get('contact_mail'))
                ->subject($subject)
                ->replyTo(Option::get('contact_mail'), $name)
                ->body($content, $mailsData)
                ->send();
        }

        if($form->send_telegram == 1 && Plugin::isActive('telegram'))
        {
            defer(function () use ($request, $form) {

                $message = ":::📝 ".$form->name.":\n";

                $message .= "Ngày: ".date('d-m-Y H:i')."\n";

                $message .= "-------------------\n";

                $fields = unserialize($form->field);

                foreach (($fields['default'] ?? []) as $column => $input)
                {
                    if(!empty($input['use']))
                    {
                        $message .= $input['label'].": {$request->input($input['field'])}\n";
                    }
                }

                foreach (($fields['metadata'] ?? []) as $column => $input)
                {
                    $message .= $input['label'].": {$request->input($input['field'])}\n";
                }

                $message .= "-------------------\n";

                $message .= "Thông tin được gửi từ website ".Url::base()."\n";

                \SkillDo\Telegram\Notification::make()->message($message)->send();

            }, 'telegram_form_register_created');
        }

        $result['data'] 	    = [];

        $result['is_redirect'] 	= false;

        if($form->is_redirect == 1 && !empty($form->url_redirect))
        {
            $result['is_redirect'] 	= true;

            $result['url_redirect'] = $form->url_redirect;
        }

        do_action('generate_form_register_success', $form, $mailsData);

        response()->success(trans('generate-form-register::register.gfr.success'), $result);
    }
}