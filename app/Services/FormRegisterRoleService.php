<?php
namespace GenerateFormRegister\Services;

class FormRegisterRoleService
{
    public function group($group)
    {
        $group['email_register'] = [
            'label' => 'Form Đăng Ký',
            'capabilities' => array_keys(static::capabilities())
        ];
        return $group;
    }

    public function label( $label ): array
    {
        return array_merge($label, static::capabilities());
    }

    static public function capabilities()
    {
        $label['generate_form_register']      = 'Quản lý tạo form đăng ký';
        $label['view_email_register']         = 'Xem danh email';
        $label['add_email_register']          = 'Thêm email';
        $label['edit_email_register']         = 'Sửa email';
        $label['delete_email_register']       = 'Xóa email';
        return apply_filters( 'email_register_capabilities', $label );
    }
}