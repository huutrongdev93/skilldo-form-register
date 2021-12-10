<?php
Class Form_Register_Active {

    public static function active() {
        static::createDatabase();
        static::createForm();
        static::addRole();
    }

    public static function createDatabase() {
        $model = get_model('plugins', 'backend');
        if(!$model->db_table_exists('generate_form_register')) {
            $model->query("CREATE TABLE `".CLE_PREFIX."generate_form_register` (
                `id` int(11) NOT NULL,
                `is_live` tinyint(4) NOT NULL DEFAULT '1',
                `send_email` tinyint(4) NOT NULL DEFAULT '0',
                `is_redirect` tinyint(4) NOT NULL DEFAULT '0',
                `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `key` varchar(255) NOT NULL,
                `field` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `taxonomy` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `taxonomy_icon` varchar(255) NOT NULL,
                `url_redirect` varchar(255) NOT NULL,
                `taxonomy_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `email_template` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
                `order` int(11) NOT NULL DEFAULT '0',
                `created` datetime DEFAULT NULL,
                `updated` datetime DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            $model->query("ALTER TABLE `".CLE_PREFIX."generate_form_register` ADD PRIMARY KEY (`id`);");
            $model->query("ALTER TABLE `".CLE_PREFIX."generate_form_register` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;");
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
            if(Form_Register::count(['where' => ['key' => $form['key']]]) == 0) {
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