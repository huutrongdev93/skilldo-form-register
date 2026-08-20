<?php
namespace FormRegister\Models;

use SkillDo\Database\Eloquent\Model;

class FormRegisterResult extends Model
{
    protected string $table = 'form_register_result';

    protected array $columns = [
        'name' => ['string'],
        'email' => ['string'],
        'phone' => ['string'],
        'message' => ['string'],
        'status' => ['int', 0],
        'form_key' => ['string'],
    ];

    protected array $rules = [
        'add'               => [
            'require' => [
                'form_key' => 'Form Key không được để trống'
            ]
        ],
    ];
}