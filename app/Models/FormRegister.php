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

    /** Chuỗi email người dùng nhập (phẩy / chấm phẩy / xuống dòng) -> "a@x.vn, b@y.vn", bỏ địa chỉ sai và trùng. */
    static function normalizeEmails(string $raw): string
    {
        $list = preg_split('/[\s,;]+/', strtolower($raw), -1, PREG_SPLIT_NO_EMPTY);

        $list = array_filter($list, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));

        return implode(', ', array_unique($list));
    }

    /** Danh sách người nhận thông báo của form: meta `email_to`, trống thì email liên hệ của site. */
    static function notifyEmails(int $formId): array
    {
        $list = self::normalizeEmails((string) self::getMeta($formId, 'email_to'));

        if($list === '') $list = self::normalizeEmails((string) \Option::get('contact_mail'));

        return $list === '' ? [] : explode(', ', $list);
    }
}
