<?php
add_action('admin_navigation', [\FormRegister\Builds\RegisterContactBuild::class, 'adminNavigation']);

add_filter('manage_form_register_result_register_contact_columns', [\FormRegister\Builds\RegisterContactBuild::class, 'tableColumn']);

add_filter('single_row_form_register_result_register_contact', [\FormRegister\Builds\RegisterContactBuild::class, 'tableTrClass'], 10, 2);
