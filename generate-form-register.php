<?php
/**
Plugin name     : Generate Form Register
Plugin class    : generate_form_register
Plugin uri      : http://sikido.vn
Description     : Trình xây dựng form đăng ký
Author          : Nguyễn Hữu Trọng
Version         : 3.1.0
*/
class generate_form_register {

    private string $name = 'generate_form_register';

    function __construct() {
        add_action('admin_init', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'load_form'));
        add_action('theme_custom_script_no_tag', array($this, 'load_script'));
    }

    public function active() {
        Form_Register_Active::active();
    }

    public function uninstall() {
        $model = model();
        if($model::schema()->hasTable('generate_form_register')) {
            $model::schema()->drop('generate_form_register');
        }
    }

    public function add_admin_menu() {
        if(Auth::hasCap('generate_form_register')) {
            AdminMenu::addSub('system', 'generate_form_register', 'Quản lý form đăng ký','plugins?page=generate_form_register',[
                'callback' => 'generate_form_register'
            ]);
        }
    }

    public function load_form() {

        $forms = Form_Register::gets();

        foreach ($forms as $key => $form) {

            if($form->is_live == 1) {
                if(!empty($form->taxonomy_config)) {
                    $code = Form_Register::generateCodeTaxonomy($form);
                    $code = trim($code);
                    eval($code);
                }
            }
        }
    }

    public function load_script() {
        ?>
        <script>
            $(function(){
                $('.email-register-form').submit(function(){
                    let button = $(this).find('button[type=submit]');
                    let btnTxt = button.html();
                    button.html('<i class="fas fa-spinner fa-pulse"></i>');
                    let form = $(this);
                    let data = $(this).serializeJSON();
                    data.action = 'ajax_email_register';
                    $.post(base+'/ajax', data, function(data) {}, 'json').done(function(response) {
                        show_message(response.message, response.status);
                        button.html(btnTxt);
                        if( response.status === 'success' ) {
                            form.trigger("reset");
                            if(response.is_redirect === true) {
                                window.location.href = response.url_redirect;
                            }
                        }
                    });
                    return false;
                });
            });
        </script>
        <?php
    }
}
new generate_form_register();
/* xử lý quản lý admin */
include 'include/active.php';
include 'generate-form-register-function.php';
include 'generate-form-register-ajax.php';
/* xử lý quản lý admin */
if(Admin::is()) {
    include 'admin/generate-form-register-roles.php';
    include 'admin/generate-form-register-action-bar.php';
    include 'admin/generate-form-register.php';
}