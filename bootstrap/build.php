<?php
add_action('admin_navigation', [\FormRegister\Builds\TuVanMauBuild::class, 'adminNavigation']);

add_filter('manage_form_register_result_tu_van_mau_columns', [\FormRegister\Builds\TuVanMauBuild::class, 'tableColumn']);

add_filter('single_row_form_register_result_tu_van_mau', [\FormRegister\Builds\TuVanMauBuild::class, 'tableTrClass'], 10, 2);
