<?php

use FormRegister\Modules\Admin\Setting\FormRegisterSystem;
use FormRegister\Services\FormRegisterAdminService;
use GenerateFormRegister\Services\FormRegisterRoleService;

add_action('theme_custom_assets', [FormRegisterAdminService::class, 'web'], 20, 2);

add_action('admin_breadcrumb', [FormRegisterAdminService::class, 'breadcrumb']);

// Xoá dòng kết quả thì xoá luôn metadata của nó (ActionAjax::delete không đụng tới bảng đó).
add_action('ajax_delete_form_register_result_before_success', [FormRegisterAdminService::class, 'deleteMetadata']);

add_filter('admin_system_tabs', [FormRegisterSystem::class, 'register']);

add_filter( 'user_role_editor_group', [FormRegisterRoleService::class, 'group']);
add_filter( 'user_role_editor_label', [FormRegisterRoleService::class, 'label']);