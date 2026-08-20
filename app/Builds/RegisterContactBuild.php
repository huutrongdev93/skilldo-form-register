<?php
namespace FormRegister\Builds;

use SkillDo\Cache\Cache;
use SkillDo\Cms\Menu\AdminMenu;

class RegisterContactBuild
{
    static function adminNavigation(): void
    {
        $count =  (int)Cache::remember('generate_form_count_register_contact', config('cms.cache_time.default'), function()
        {
            return \FormRegister\Models\FormRegisterResult::where('form_key', 'register_contact')->where('status', 1)->count();
        });

        AdminMenu::addSub('marketing', 'form_register_result_register_contact','Đăng ký tư vấn', route('admin.form_register_result.index', ['form-key' => 'register_contact']), [
            'count' => $count
        ]);
    }

    static function tableColumn($columns): array
    {
        $columnsNew['cb']   	= 'cb';

        $columnsNew['name'] = [
    'label'  => 'Họ và tên',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('name', $item, $args)
];
$columnsNew['email'] = [
    'label'  => 'Email',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('email', $item, $args)
];
$columnsNew['phone'] = [
    'label'  => 'Số điện thoại',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('phone', $item, $args)
];
$columnsNew['message'] = [
    'label'  => 'Ghi chú',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('message', $item, $args)
];


        $columnsNew['created'] 	= trans('table.created');

        $columnsNew['action'] 	= trans('table.action');

        return $columnsNew;
    }

    static function tableTrClass( $columns, $item ): string
    {
        return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
    }
}


