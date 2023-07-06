<?php
class GenerateFormRegisterAdmin {
    static function register($tabs) {
        if(Auth::hasCap('generate_form_register')) {
            $tabs['generate_form_register'] = [
                'label' => 'Form đăng ký',
                'description' => 'Quản lý form đăng ký, booking',
                'callback' => 'GenerateFormRegisterAdmin::render',
                'icon' => '<i class="fad fa-mailbox"></i>',
            ];
        }
        return $tabs;
    }
    static function render(): void {

        $view = Request::get('view');

        if(empty($view)) $view = 'list';

        Plugin::partial('generate-form-register', 'admin/html/form-tabs', ['view' => $view]);

        $data = [];

        if($view == 'list') {
            $path = 'form-list';
            $data['forms'] = Form_Register::gets();
        }

        if($view == 'add') {
            $path = 'form-save';
            $data['FormBuilder'] = new FormBuilder();
        }

        if($view == 'edit') {
            $path = 'form-save';
            $data['FormBuilder'] = new FormBuilder();
            $data['formID'] = (int)Request::get('id');
            $data['form'] = Form_Register::get($data['formID']);
        }

        if($view == 'sample') {
            $path = 'form-sample';
            $data['forms'] = [
                [
                    'key'               => 'email_register',
                    'name'              => 'ĐĂNG KÝ NHẬN TIN',
                    'is_live'           => 1, 'send_email' => 0,
                    'field'             => 'email|title|Email|data|true|required,email',
                    'taxonomy'          => 'email_register', 'taxonomy_config' => "name='Đăng ký nhận tin'",
                    'sample'            => '<p>email</p>'
                ],
                [
                    'key'               => 'register_contact',
                    'name'              => 'ĐĂNG KÝ TƯ VẤN',
                    'is_live'           => 1, 'send_email'  => 0,
                    'field'             => "name|title|Họ và Tên|data|true|required\nemail|excerpt|Email|data|true|required,email\nphone|content|Số điện thoại|data|true|required\nnote|seo_title|Ghi chú|data|true",
                    'taxonomy'          => 'register_contact', 'taxonomy_config' => "name='Đăng ký tư vấn'",
                    'sample'            => ' <p>name</p> <p>email</p> <p>phone</p> <p>note</p>'
                ],
                [
                    'key'               => 'register_booking',
                    'name'              => 'BOOKING',
                    'is_live'           => 1, 'send_email'  => 0,
                    'field'             => "name|title|Họ và tên|data|true|required\nemail|excerpt|Email|data|true|required,email\nphone|content|Số điện thoại|data|true|required\ntime|content|Giờ|data|true|required\ndate|seo_title|Ngày|data|true|required",
                    'taxonomy'          => 'register_booking', 'taxonomy_config' => "name='Booking'",
                    'sample'            => ' <p>name</p> <p>email</p> <p>phone</p> <p>time</p> <p>date</p>'
                ],
            ];
        }

        if(!empty($path)) {
            Plugin::partial('generate-form-register', 'admin/html/'.$path, $data);
        }
    }
    static function save($result) {

        $data = Request::post();

        unset($data['action']);

        unset($data['post_type']);

        unset($data['cate_type']);

        if(!empty($data['id'])) {

            $id = (int)Request::post('id');

            $form   = Form_Register::get($id);

            if(!have_posts($form)) {
                $result['status'] = 'errpr';
                $result['message'] = 'Form không tồn tại';
                return $result;
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
            else {
                $result['status'] = 'error';
                $result['message'] = 'Lưu dữ liệu thất bại';
            }
        }
        else {

            $key = Request::post('key');

            if(empty($key)) {
                $result['status'] = 'error';
                $result['message'] = 'Key form không được để trống';
                return $result;
            }

            if(Form_Register::count(Qr::set('key', $key)) != 0) {
                $result['status'] = 'error';
                $result['message'] = 'Key form đã tồn tại';
                return $result;
            }

            if(!isset($data['is_live'])) $data['is_live'] = 1;

            if(Form_Register::insert($data)) {
                $result['status'] = 'success';
                $result['message'] = 'Lưu dữ liệu thành công.';
            }
            else {
                $result['status'] = 'error';
                $result['message'] = 'Thêm dữ liệu thất bại';
            }
        }

        return $result;
    }
    static function count(): void {
        if(Template::isPage('post_index')) {
            $post_type = Admin::getPostType();
            if(!empty($post_type) && $post_type != 'post' && Form_Register::count(Qr::set('taxonomy',$post_type)) != 0) {
                model('post')->update(['status' => 0], Qr::set('post_type', $post_type)->where('status', 1));
                CacheHandler::save('generate_form_count_'.$post_type, 0);
            }
        }
        ?>
        <style>
            .table.table tr.new td {
                background-color: #e1f1ea;
            }
        </style>
        <?php
    }
}

add_filter('skd_system_tab', 'GenerateFormRegisterAdmin::register', 50);
add_filter('system_generate_form_register_save', 'GenerateFormRegisterAdmin::save', 50);
add_action('admin_footer', 'GenerateFormRegisterAdmin::count');