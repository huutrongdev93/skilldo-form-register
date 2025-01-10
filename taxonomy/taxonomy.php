function form_register_result_{{formKey}}_menu() {

	$cacheId = 'generate_form_count_{{formKey}}';

	$count =  \SkillDo\Cache::get($cacheId);

	if(!is_numeric($count)) {

		$count = \FormRegister\Model\FormResult::count(Qr::set('form_key', '{{formKey}}')->where('status', 1));

		\SkillDo\Cache::save($cacheId, $count);
	}

	AdminMenu::addSub('marketing', 'form_register_result_{{formKey}}','{{name}}', 'plugins/form_register_result?form-key={{formKey}}', [
		'count' => $count
	]);
}
add_action('admin_init', 'form_register_result_{{formKey}}_menu');

function form_register_result_{{formKey}}_column( $columns ): array
{
    $columnsNew['cb']   	= 'cb';
	{{columnsNew}}
    $columnsNew['created'] 	= trans('table.created');
    $columnsNew['action'] 	= trans('table.action');
    return $columnsNew;
}
add_filter('manage_form_register_result_{{formKey}}_columns', 'form_register_result_{{formKey}}_column', 10);

function form_register_result_{{formKey}}_single_row( $columns, $item ) {
	return '<tr class="tr_'.$item->id.' '.(($item->status == 1) ? 'new' : '').'">';
}
add_filter('single_row_form_register_result_{{formKey}}', 'form_register_result_{{formKey}}_single_row', 10, 2);
