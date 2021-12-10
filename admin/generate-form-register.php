<?php
function generate_form_register() {

    $action = InputBuilder::Get('action');

    $tab = InputBuilder::Get('tab');

    if(empty($tab)) {

        if($action == 'add_form') {
            $FormBuilder = new FormBuilder();
        }
        else if($action == 'edit_form') {
            $form_id = InputBuilder::get('form_id');
            $form = Form_Register::get($form_id);
            $FormBuilder = new FormBuilder();
        }
        else {
            $forms = Form_Register::gets();
        }
    }
    if($tab == 'sample') {
        $forms = [
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

    include 'html/html-form-dashboard.php';
}

function generate_form_register_footer() {

    if(Template::isPage('post_index')) {

        $post_type = Admin::getPostType();

        if(!empty($post_type) && $post_type != 'post' && Form_Register::count(['where' => array('taxonomy' => $post_type)]) != 0) {
            get_model()->settable('post')->update_where(['status' => 0], ['post_type' => $post_type, 'status' => 1]);
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

add_action('admin_footer', 'generate_form_register_footer');