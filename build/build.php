<?php
namespace FormRegister\Builds;

use SkillDo\Cache\Cache;
use SkillDo\Cms\Menu\AdminMenu;

class FORM_KEY_CLASS_NAMEBuild
{
    static function adminNavigation(): void
    {
        $count =  (int)Cache::remember('generate_form_count_FORM_KEY', config('cms.cache_time.default'), function()
        {
            return \FormRegister\Models\FormRegisterResult::where('form_key', 'FORM_KEY')->where('status', 1)->count();
        });

        AdminMenu::addSub('marketing', 'form_register_result_FORM_KEY','FORM_NAME', route('admin.form_register_result.index', ['form-key' => 'FORM_KEY']), [
            'count' => $count
        ]);
    }

    static function tableColumn($columns): array
    {
        $columnsNew['cb']   	= 'cb';

        COLUMNS_NEW

        $columnsNew['created'] 	= trans('table.created');

        $columnsNew['action'] 	= trans('table.action');

        return $columnsNew;
    }

    static function tableTrClass( $columns, $item ): string
    {
        return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
    }
}


