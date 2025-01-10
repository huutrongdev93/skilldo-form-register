<?php
const G_FORM_REGISTER_VERSION = '4.1.0';

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

include 'autoload/autoload.php';

if(Admin::is())
{
	if(file_exists('views/plugins/generate-form-register/taxonomy/taxonomy.build.php'))
    {
        include 'taxonomy/taxonomy.build.php';
	}
}