<?php
class AdminFormRegisterResultButton {
    /**
     * Thêm buttons action cho header của table
     * @param $buttons
     * @return array
     */
    static function tableHeaderButton($buttons): array
    {
        $buttons[] = Admin::button('blue', [
            'id' => 'js_export_form_register_result_btn_modal',
            'icon' => '<i class="fa-light fa-download"></i>',
            'text' => trans('export.data')
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
add_filter('table_form_register_result_header_buttons', 'AdminFormRegisterResultButton::tableHeaderButton');
add_filter('table_form_register_result_bulk_action_buttons', 'AdminFormRegisterResultButton::bulkAction', 30);