<?php

namespace FormRegister\Controllers\Admin;

use Admin\Supports\FormAdminHelper;
use SkillDo\Cms\Controller;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Http\Request;

class FormRegisterResultController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        Cms::setData('module', 'form_register_result');
    }

    public function index(Request $request)
    {
        $formKey = $request->input('form-key');

        if(empty($formKey))
        {
            return Admin::pageNotFound();
        }

        $form = \FormRegister\Models\FormRegister::where('key', $formKey)->select('name')->first();

        if(empty($form))
        {
            return Admin::pageNotFound();
        }

        Cms::setData('table', new \FormRegister\Modules\Admin\FormRegisterResult\FormRegisterResultTable());

        Cms::setData('form', $form);

        return Cms::view('generate-form-register::admin/result/index');
    }
}