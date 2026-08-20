<?php
namespace FormRegister\Services;

use Illuminate\Database\Schema\Blueprint;
use SkillDo\Cms\Support\Role;
use Illuminate\Support\Facades\DB;

Class ActivatorService
{
    public static function activate(): void
    {
        if(!schema()->hasTable('generate_form_register'))
        {
            schema()->create('generate_form_register', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('is_live')->default(1);
                $table->tinyInteger('send_email')->default(0);
                $table->tinyInteger('send_telegram')->default(0);
                $table->tinyInteger('is_redirect')->default(0);
                $table->string('name', 200)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('key', 100);
                $table->text('field')->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('url_redirect', 255);
                $table->text('email_template')->collation('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
            });
        }

        if(!schema()->hasTable('form_register_result'))
        {
            schema()->create('form_register_result', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 100)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('phone', 50)->collation('utf8mb4_unicode_ci')->nullable();
                $table->text('message')->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('form_key', 50)->collation('utf8mb4_unicode_ci');
                $table->integer('status')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
                $table->index('id');
                $table->index('form_key');
            });
        }

        if(!schema()->hasTable('form_register_result_metadata'))
        {
            schema()->create('form_register_result_metadata', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('object_id')->default(0);
                $table->string('meta_key', 100)->collation('utf8mb4_unicode_ci')->nullable();
                $table->longText('meta_value')->collation('utf8mb4_unicode_ci')->nullable();
                $table->integer('order')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
                $table->index('object_id');
                $table->index('meta_key');
            });
        }

        $forms = [
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

        foreach ($forms as $form)
        {
            if(\FormRegister\Models\FormRegister::where('key', $form['key'])->count() == 0)
            {
                \FormRegister\Models\FormRegister::create($form);
            }
        }

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