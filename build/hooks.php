add_action('admin_navigation', [\FormRegister\Builds\FORM_KEY_CLASS_NAMEBuild::class, 'adminNavigation']);

add_filter('manage_form_register_result_FORM_KEY_columns', [\FormRegister\Builds\FORM_KEY_CLASS_NAMEBuild::class, 'tableColumn']);

add_filter('single_row_form_register_result_FORM_KEY', [\FormRegister\Builds\FORM_KEY_CLASS_NAMEBuild::class, 'tableTrClass'], 10, 2);