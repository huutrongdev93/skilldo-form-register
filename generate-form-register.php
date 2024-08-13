<?php
const G_FORM_REGISTER_VERSION = '4.0.1';

class generate_form_register {

    private string $name = 'generate_form_register';

    function __construct() {
        add_action('theme_custom_assets', array($this, 'assets'), 20, 2);
    }

    public function active(): void
    {
        (include 'database/database.php')->up();
        Form_Register_Active::active();
    }

    public function uninstall(): void
    {
        (include 'database/database.php')->down();
    }

    public function assets(AssetPosition $header, AssetPosition $footer): void {
        $footer->add('generate_form_register', 'views/plugins/generate-form-register/assets/form-register-script.js', ['minify' => true]);
    }
}
new generate_form_register();
/* xử lý quản lý admin */
include 'update.php';
include 'include/active.php';
include 'generate-form-register-function.php';
include 'generate-form-register-ajax.php';
/* xử lý quản lý admin */
if(Admin::is()) {
    include 'admin/generate-form-register-roles.php';
    include 'admin/generate-form-register.php';
    include 'admin/form-register-result.php';
	if(file_exists('views/plugins/generate-form-register/taxonomy/taxonomy.build.php')) {
        include 'taxonomy/taxonomy.build.php';
	}
}