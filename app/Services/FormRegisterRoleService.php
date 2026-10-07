<?php
namespace FormRegister\Services;

class FormRegisterRoleService
{
    static public function group($group)
    {
        $group['email_register'] = [
            'label' => 'Form Đăng Ký',
            'capabilities' => array_keys(static::capabilities())
        ];
        return $group;
    }

    static public function label( $label ): array
    {
        return array_merge($label, static::capabilities());
    }

    /**
     * Quyền vào từng trang admin của plugin (filter role_editor_admin_route_caps của user-role-editor).
     */
    static public function routeCaps($caps): array
    {
        $caps['admin.formRegister.index']        = 'generate_form_register';
        $caps['admin.formRegister.add']          = 'generate_form_register';
        $caps['admin.formRegister.edit']         = 'generate_form_register';
        $caps['admin.formRegister.sample']       = 'generate_form_register';
        $caps['admin.form_register_result.index'] = 'view_email_register';
        return $caps;
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