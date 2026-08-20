{!! Admin::partial('resources/page-default/page-index', [
    'name' => $form->name,
    'module' => $module,
    'table' => $table
]) !!}

{!! view('generate-form-register::admin/result/export/modal') !!}

@php
    $cacheId = 'generate_form_count_'.$form->key;

    $count =  \SkillDo\Cache\Cache::get($cacheId);

    if(!is_numeric($count) || $count > 0)
    {
        \FormRegister\Models\FormRegisterResult::where('status', 1)->update(['status' => 0]);

        \SkillDo\Cache\Cache::delete($cacheId);
    }
@endphp