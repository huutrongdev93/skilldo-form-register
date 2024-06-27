<?php
function form_register_result_product_form_contact_menu() {

	$cacheId = 'generate_form_count_product_form_contact';

	$count =  CacheHandler::get($cacheId);

	if(!is_numeric($count)) {

		$count = Form_Register_Result::count(Qr::set('form_key', 'product_form_contact')->where('status', 1));

		CacheHandler::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_product_form_contact','Đăng ký liên hệ tư vấn', 'plugins/form_register_result?form-key=product_form_contact', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_product_form_contact_menu');

function form_register_result_product_form_contact_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['message'] = [
    'label'  => '',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('message', $item, $args)
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

function form_register_result_email_register_menu() {

	$cacheId = 'generate_form_count_email_register';

	$count =  CacheHandler::get($cacheId);

	if(!is_numeric($count)) {

		$count = Form_Register_Result::count(Qr::set('form_key', 'email_register')->where('status', 1));

		CacheHandler::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_email_register','Đăng ký nhận tin', 'plugins/form_register_result?form-key=email_register', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_email_register_menu');

function form_register_result_email_register_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	$columnsNew['message'] = [
    'label'  => '',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('message', $item, $args)
];

    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_email_register_columns', 'form_register_result_email_register_column', 10);

function form_register_result_email_register_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_email_register', 'form_register_result_email_register_single_row', 10, 2);

