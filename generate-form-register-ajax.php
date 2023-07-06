<?php
Class Form_Register_Ajax {
    static public function register($ci, $model) {
        $result['status'] 	= 'error';

        $result['message'] 	= 'Đăng ký không thành công!';

        if(Request::Post()) {

            $form_key = Request::post('form_key');

            $form 	  = Form_Register::getBy('key', $form_key);

            $post 	  = Request::post();

            unset($post['action']);

            if(have_posts($form) && have_posts($post)) {

                if($form->is_live == 0) {
                    $result['message'] 	= 'Thật xin lỗi! Form này đã kết thúc chương trình đăng ký!';
                    echo json_encode($result);
                    return false;
                }

                $fields = Form_Register::config($form->field);

                if(have_posts($fields)) {

                    $errors = '';

                    $errors = apply_filters('generate_form_register_'.$form_key.'_error', $errors, $form );

                    if(is_skd_error($errors)) {
                        foreach ($errors->errors as $key => $error) {
                            $result['message'] 	= $error[0];
                        }
                        echo json_encode($result); return true;
                    }

                    $data 		= [];

                    $metadata 	= [];

                    foreach ($fields as $input_name => $input) {
                        if(isset($post[$input_name])) {
                            if($input['type'] == 'data') {
                                $data[$input['field']] 			= Str::clear($post[$input_name]);
                            }
                            if($input['type'] == 'metadata') {
                                $metadata[$input['field']] 		= Str::clear($post[$input_name]);
                            }
                            if(isset($input['rule']) && have_posts($input['rule'])) {
                                if(in_array("required", $input['rule']) !== false) {
                                    if(empty($post[$input_name])) {
                                        $errors = new SKD_Error('invalid_'.$input['field'].'_required', __('Trường '.$input['label'].' không được để trống.'));
                                        break;
                                    }
                                }
                                if(in_array("email", $input['rule']) !== false) {
                                    if(!filter_var($post[$input_name], FILTER_VALIDATE_EMAIL)) {
                                        $errors = new SKD_Error('invalid_'.$input['field'].'_required', __('Trường '.$input['label'].' không đúng định dạng.'));
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    if(is_skd_error($errors)) {
                        foreach ($errors->errors as $key => $error) {
                            $result['message'] 	= $error[0];
                        }
                        echo json_encode($result); return true;
                    }

                    $data['public'] 	= 0;

                    $data['status'] 	= 1;

                    $data['post_type'] = $form->taxonomy;

                    $data = apply_filters('generate_form_register_'.$form_key.'_data', $data, $form );

                    $res  = Posts::insert($data);

                    if(!is_skd_error($res)) {

                        CacheHandler::delete('generate_form_count_'.$form->taxonomy);

                        if(have_posts($metadata)) {
                            foreach ($metadata as $meta_key => $meta_value) {
                                Posts::updateMeta($res, $meta_key, $meta_value);
                            }
                        }

                        if($form->send_email == 1) {

                            $name =  'No name';

                            if(!empty($post['name'])) {
                                $name = str::clear($post['name']);
                            }
                            else if(!empty($post['fullname'])) {
                                $name = str::clear($post['fullname']);
                            }

                            $content = file_get_contents(FCPATH.Path::plugin('generate-form-register').'/email-template/template-1.php');

                            $content = str_replace('{{email_template}}', $form->email_template, $content);

                            $post['base_url'] = Url::base();

                            $post = apply_filters('generate_form_register_'.$form_key.'_email_data', $post, $form );

                            EmailHandler::send($content, $form->name.'-'.date('d/m/Y'), [
                                'name'      => $name,
                                'from'      => option::get('contact_mail'),
                                'address'   => option::get('contact_mail'),
                                'templateValue' => $post,
                            ]);
                        }

                        $result['is_redirect'] 	= false;

                        if($form->is_redirect == 1 && !empty($form->url_redirect)) {

                            $result['is_redirect'] 	= true;

                            $result['url_redirect'] = $form->url_redirect;
                        }

                        do_action('generate_form_register_success', $form);

                        $result['status'] 	= 'success';

                        $result['message'] 	= 'Đăng ký thành công!';
                    }
                }
            }
        }

        echo json_encode($result);
    }
}
Ajax::client('Form_Register_Ajax::register');

function ajax_email_register($ci, $model): void {
    Form_Register_Ajax::register($ci, $model);
}
Ajax::client('ajax_email_register');