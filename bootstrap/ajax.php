<?php

use FormRegister\Ajax\Web\FormRegisterAjax;
use SkillDo\Cms\Support\Ajax;

Ajax::client('FormRegister\Ajax\Web\FormRegisterAjax::register');
Ajax::admin('FormRegister\Ajax\Admin\FormRegisterAjax::adminSave');
Ajax::admin('FormRegister\Ajax\Admin\FormRegisterAjax::quickCreate');
Ajax::admin('FormRegister\Ajax\Admin\FormRegisterAjax::export');

function ajax_email_register(\SkillDo\Http\Request $request): void
{
    FormRegisterAjax::register($request);
}

Ajax::client('ajax_email_register');