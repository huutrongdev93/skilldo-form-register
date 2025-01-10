<?php
Class Form_Register_Active {

    public static function active(): void
    {
        static::createForm();
        static::addRole();
    }

    public static function createForm(): void
    {
        $forms_default = [
            [
                'key'       => 'email_register',
                'name'      => 'Đăng ký nhận tin',
                'is_live'   => 0,
                'send_email'=> 0,
                'field'     => [
                    "default" => [
                        'name' => [
                            "use" => "0",
                            "field" => "name",
                            "label" => "",
                            "required" => "1",
                            "limit" => "0",
                        ],
                        'email' => [
                            "use" => "1",
                            "field" => "email",
                            "label" => "Email",
                            "required" => "1",
                            "isEmail" => "0",
                        ],
                        'phone' => [
                            "use"   => "0",
                            "field" => "phone",
                            "label" => "Số điện thoại",
                            "required" => "1",
                            "isPhone" => "0",
                        ],
                        'message' => [
                            "use"   => "0",
                            "field" => "note",
                            "label" => "Ghi chú",
                            "required" => "1",
                        ],
                    ],
                    'metadata'  => [],
                ],
            ]
        ];

        foreach ($forms_default as $key => $form) {
            if(\FormRegister\Model\Form::count(Qr::where('key', $form['key'])) == 0) {
                \FormRegister\Model\Form::insert($form);
            }
        }
    }

    public static function addRole(): void
    {
        // Add caps for Root role
        $role = Role::get('root');
        $role->add('view_email_register');
        $role->add('add_email_register');
        $role->add('edit_email_register');
        $role->add('delete_email_register');
        $role->add('generate_form_register');
        // Add caps for Administrator role
        $role = Role::get('administrator');
        $role->add('view_email_register');
        $role->add('add_email_register');
        $role->add('edit_email_register');
        $role->add('delete_email_register');
    }
}