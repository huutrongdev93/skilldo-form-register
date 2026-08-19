<?php
function form_register_result_register_contact_menu() {

	$cacheId = 'generate_form_count_register_contact';

	$count =  \SkillDo\Cache::get($cacheId);

	if(!is_numeric($count)) {

		$count = \FormRegister\Model\FormResult::count(Qr::set('form_key', 'register_contact')->where('status', 1));

		\SkillDo\Cache::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_register_contact','Đăng ký tư vấn', 'plugins/form_register_result?form-key=register_contact', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_register_contact_menu');

function form_register_result_register_contact_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['name'] = [
    'label'  => 'Họ và tên',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('name', $item, $args)
];
$columnsNew['phone'] = [
    'label'  => 'Số điện thoại',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('phone', $item, $args)
];
$columnsNew['message'] = [
    'label'  => 'Ghi chú',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('message', $item, $args)
];

    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_register_contact_columns', 'form_register_result_register_contact_column', 10);

function form_register_result_register_contact_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_register_contact', 'form_register_result_register_contact_single_row', 10, 2);

function form_register_result_marketing_support_center_menu() {

	$cacheId = 'generate_form_count_marketing_support_center';

	$count =  \SkillDo\Cache::get($cacheId);

	if(!is_numeric($count)) {

		$count = \FormRegister\Model\FormResult::count(Qr::set('form_key', 'marketing_support_center')->where('status', 1));

		\SkillDo\Cache::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_marketing_support_center','Đăng ký tư vấn', 'plugins/form_register_result?form-key=marketing_support_center', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_marketing_support_center_menu');

function form_register_result_marketing_support_center_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['phone'] = [
    'label'  => 'Số điện thoại',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('phone', $item, $args)
];
$columnsNew['url'] = [
    'label'  => 'Liên kết',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('url', $item, $args)->value(function($item, \SkillDo\Table\Columns\ColumnText $column) {
        $metadata = \FormRegister\Model\FormResult::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_marketing_support_center_data', $metadata, $column);
    })
];

    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_marketing_support_center_columns', 'form_register_result_marketing_support_center_column', 10);

function form_register_result_marketing_support_center_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_marketing_support_center', 'form_register_result_marketing_support_center_single_row', 10, 2);

function form_register_result_register_booking_menu() {

	$cacheId = 'generate_form_count_register_booking';

	$count =  \SkillDo\Cache::get($cacheId);

	if(!is_numeric($count)) {

		$count = \FormRegister\Model\FormResult::count(Qr::set('form_key', 'register_booking')->where('status', 1));

		\SkillDo\Cache::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_register_booking','BOOKING', 'plugins/form_register_result?form-key=register_booking', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_register_booking_menu');

function form_register_result_register_booking_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['name'] = [
    'label'  => 'Họ và tên',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('name', $item, $args)
];
$columnsNew['email'] = [
    'label'  => 'Email',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('email', $item, $args)
];
$columnsNew['phone'] = [
    'label'  => 'Số điện thoại',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('phone', $item, $args)
];
$columnsNew['time'] = [
    'label'  => 'Giờ',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('time', $item, $args)->value(function($item, \SkillDo\Table\Columns\ColumnText $column) {
        $metadata = \FormRegister\Model\FormResult::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_register_booking_data', $metadata, $column);
    })
];
$columnsNew['data'] = [
    'label'  => 'Ngày',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('data', $item, $args)->value(function($item, \SkillDo\Table\Columns\ColumnText $column) {
        $metadata = \FormRegister\Model\FormResult::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_register_booking_data', $metadata, $column);
    })
];

    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_register_booking_columns', 'form_register_result_register_booking_column', 10);

function form_register_result_register_booking_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_register_booking', 'form_register_result_register_booking_single_row', 10, 2);

function form_register_result_product_form_contact_menu() {

	$cacheId = 'generate_form_count_product_form_contact';

	$count =  \SkillDo\Cache::get($cacheId);

	if(!is_numeric($count)) {

		$count = \FormRegister\Model\FormResult::count(Qr::set('form_key', 'product_form_contact')->where('status', 1));

		\SkillDo\Cache::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_product_form_contact','Đăng ký liên hệ tư vấn', 'plugins/form_register_result?form-key=product_form_contact', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_product_form_contact_menu');

function form_register_result_product_form_contact_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['phone'] = [
    'label'  => 'Số điện thoại',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('phone', $item, $args)
];
$columnsNew['url'] = [
    'label'  => 'Liên kết',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('url', $item, $args)->value(function($item, \SkillDo\Table\Columns\ColumnText $column) {
        $metadata = \FormRegister\Model\FormResult::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_product_form_contact_data', $metadata, $column);
    })
];

    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_product_form_contact_columns', 'form_register_result_product_form_contact_column', 10);

function form_register_result_product_form_contact_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_product_form_contact', 'form_register_result_product_form_contact_single_row', 10, 2);

