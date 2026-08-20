<?php
namespace FormRegister\Models;

use SkillDo\Database\Eloquent\Model;

Class FormRegister extends Model
{
    protected string $table = 'generate_form_register';

    protected array $columns = [
        'name'  => ['string'],
        'key'   => ['string'],
        'field' => ['array', []],
        'url_redirect'  => ['string'],
        'email_template' => ['wysiwyg'],
        'is_live'   => ['int', 1],
        'is_redirect' => ['int', 0],
        'send_email' => ['int', 0],
        'send_telegram' => ['int', 0],
    ];

    protected array $rules = [
        'add'               => [
            'require' => [
                'key' => 'Form Key không được để trống'
            ]
        ],
    ];
}