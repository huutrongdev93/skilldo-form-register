<?php
Class Form_Register_Ajax {
    static public function add($ci, $model) {
        $result['status'] = 'error';
        $result['message'] = 'Lưu dữ liệu không thành công.';
        if(InputBuilder::Post()) {

            $data = InputBuilder::Post();

            unset($data['action']);
            unset($data['post_type']);
            unset($data['cate_type']);

            $key = InputBuilder::Post('key');

            if(empty($key)) {
                $result['message'] = 'Key form không được để trống';
                echo json_encode($result);
                return false;
            }

            if(Form_Register::count(['where' => ['key' => $key]]) != 0) {
                $result['message'] = 'Key form đã tồn tại';
                echo json_encode($result);
                return false;
            }

            if(!isset($data['is_live'])) $data['is_live'] = 1;

            if(Form_Register::insert($data)) {

                $result['status'] = 'success';

                $result['message'] = 'Lưu dữ liệu thành công.';
            }
        }
        echo json_encode($result);
    }
    static public function save($ci, $model) {

        $result['status'] = 'error';

        $result['message'] = 'Lưu dữ liệu không thành công.';

        if(InputBuilder::Post()) {

            $data = InputBuilder::Post();

            unset($data['action']);
            unset($data['post_type']);
            unset($data['cate_type']);

            if(!empty($data['id'])) {

                $id 	= (int)Str::clear($data['id']);

                $form   = Form_Register::get($id);

                if(!have_posts($form)) {
                    echo json_encode($result);
                    return false;
                }
            }

            $form_data = [];

            foreach ($data as $key => $value) {
                if($key  == 'email_template') {
                    $form_data[$key] = $value;
                }
                else {
                    $form_data[$key] = trim(Str::clear($value));
                }
            }

            if(!isset($form_data['is_live'])) $form_data['is_live'] = 0;

            if(!isset($form_data['send_email'])) $form_data['send_email'] = 0;

            if(!isset($form_data['is_redirect'])) $form_data['is_redirect'] = 0;

            if(Form_Register::insert($form_data)) {
                $result['status'] = 'success';
                $result['message'] = 'Lưu dữ liệu thành công.';
            }
        }

        echo json_encode($result);
    }
}
Ajax::admin('Form_Register_Ajax::add');
Ajax::admin('Form_Register_Ajax::save');

function ajax_email_register($ci, $model) {

	$result['status'] 	= 'error';

	$result['message'] 	= 'Đăng ký không thành công!';

	if(InputBuilder::Post()) {

		$form_key = InputBuilder::post('form_key');

		$form 	  = Form_Register::getBy('key', $form_key);

		$post 	  = InputBuilder::post();

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

					$result['status'] 	= 'success';

					$result['message'] 	= 'Đăng ký thành công!';
				}
			}
		}
	}

	echo json_encode($result);
}
Ajax::client('ajax_email_register');