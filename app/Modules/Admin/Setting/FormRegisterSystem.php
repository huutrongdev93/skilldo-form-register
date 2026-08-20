<?php
namespace FormRegister\Modules\Admin\Setting;

use SkillDo\Http\Request;
use SkillDo\Support\Auth;

class FormRegisterSystem
{
    static function register($tabs)
    {
        if(Auth::hasCap('generate_form_register'))
        {
            $tabs['generate_form_register'] = [
                'group'         => 'marketing',
                'label'         => 'Form đăng ký',
                'description'   => 'Quản lý form đăng ký, booking',
                'href'          => route('admin.formRegister.index'),
                'icon'          => '<i class="fad fa-mailbox"></i>',
            ];
        }
        return $tabs;
    }
}