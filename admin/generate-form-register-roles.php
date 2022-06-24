<?php
Class Form_Register_Role {
    function __construct() {
        add_filter( 'user_role_editor_group', [$this, 'addGroup'], 1 );
        add_filter( 'user_role_editor_label', [$this, 'addLabel'], 1 );
    }
    public function addGroup( $group ) {
        $group['email_register'] = array(
            'label' => __('Đăng ký email'),
            'capabilities' => array_keys(static::capabilities())
        );
        return $group;
    }
    public function addLabel( $label ) {
        return array_merge($label, static::capabilities());
    }
    static public function capabilities() {
        $label['generate_form_register']      = 'Quản lý tạo form đăng ký';
        $label['view_email_register']         = 'Xem danh email';
        $label['add_email_register']          = 'Thêm email';
        $label['edit_email_register']         = 'Sửa email';
        $label['delete_email_register']       = 'Xóa email';
        return apply_filters( 'email_register_capabilities', $label );
    }
}

new Form_Register_Role();
