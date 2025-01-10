<?php
class AdminFormRegisterButton {
    /**
     * Thêm buttons action cho header của table
     * @param $buttons
     * @return array
     */
    static function tableHeaderButton($buttons): array
    {
        $buttons['add'] = \Admin::button('green', [
            'href' => Url::admin('system/generate_form_register?view=add'),
            'icon' => Admin::icon('add'),
            'text' => trans('button.add')
        ]);
        $buttons['add-speed'] = \Admin::button('blue', [
            'href' => Url::admin('system/generate_form_register?view=sample'),
            'icon' => Admin::icon('add'),
            'text' => 'Thêm nhanh'
        ]);
        $buttons[] = Admin::button('reload');

        return $buttons;
    }

    /**
     * Thêm buttons cho hành dộng hàng loạt
     * @param array $actionList
     * @return array
     */
    static function bulkAction(array $actionList): array
    {
        return $actionList;
    }
}
add_filter('table_form_register_header_buttons', 'AdminFormRegisterButton::tableHeaderButton');
add_filter('table_form_register_bulk_action_buttons', 'AdminFormRegisterButton::bulkAction', 30);