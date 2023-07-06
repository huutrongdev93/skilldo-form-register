<?php
Class Form_Register_Active {

    public static function active() {
        static::createDatabase();
        static::createForm();
        static::addRole();
    }

    public static function createDatabase() {
        $model = model();
        if(!$model::schema()->hasTable('generate_form_register')) {
            $model::schema()->create('generate_form_register', function ($table) {
                $table->increments('id');
                $table->tinyInteger('is_live')->default(1);
                $table->tinyInteger('send_email')->default(0);
                $table->tinyInteger('is_redirect')->default(0);
                $table->string('name', 200)->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('key', 100);
                $table->text('field')->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('taxonomy')->collate('utf8mb4_unicode_ci')->nullable();
                $table->string('taxonomy_icon', 255);
                $table->string('url_redirect', 255);
                $table->text('taxonomy_config')->collate('utf8mb4_unicode_ci')->nullable();
                $table->text('email_template')->collate('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
            });
        }
    }

    public static function createForm() {
        $forms_default = [
            array(
                'key'               => 'email_register',
                'name'              => 'ĐĂNG KÝ NHẬN TIN',
                'is_live'           => 0,
                'send_email'        => 0,
                'field'             => 'email|title|Email|data|true|required,email',
                'metadata'          => '',
                'taxonomy'          => 'email_register',
                'taxonomy_config'   => "name='Đăng ký nhận tin'",
            )
        ];

        foreach ($forms_default as $key => $form) {
            if(Form_Register::count(Qr::set('key', $form['key'])) == 0) {
                Form_Register::insert($form);
            }
        }
    }

    public static function addRole() {
        // Add caps for Root role
        $role = role::get('root');
        $role->add_cap('view_email_register');
        $role->add_cap('add_email_register');
        $role->add_cap('edit_email_register');
        $role->add_cap('delete_email_register');
        $role->add_cap('generate_form_register');
        // Add caps for Administrator role
        $role = role::get('administrator');
        $role->add_cap('view_email_register');
        $role->add_cap('add_email_register');
        $role->add_cap('edit_email_register');
        $role->add_cap('delete_email_register');
    }
}