<?php
/**
 * FORM
 */
function action_bar_generate_form_register_button($module) {
	$ci =& get_instance();
	if($ci->template->class == 'plugins' && $ci->input->get('page') == 'generate_form_register') {
        echo '<div class="pull-left">'; do_action('action_bar_generate_form_register_left', $module); echo '</div>';
        echo '<div class="pull-right">'; do_action('action_bar_generate_form_register_right', $module); echo '</div>';
    }
}

function action_bar_generate_form_register_button_right($module) {

    $action = InputBuilder::Get('action');

    if(empty($action)) {
        ?>
        <a href="<?php echo Url::admin('plugins?page=generate_form_register&action=add_form');?>" class="btn-icon btn-green"><i class="fal fa-plus"></i> Thêm Mới</a>
        <?php
    }
    if($action == 'add_form' || $action == 'edit_form') {
        ?>
        <button name="save" class="btn-icon btn-green" form="generate_form_register"><?php echo Admin::icon('save');?> Lưu</button>
        <a href="<?php echo Url::admin('plugins?page=generate_form_register');?>" class="btn-icon btn-blue"><?php echo Admin::icon('back');?> Quay lại</a>
        <?php
    }
}
add_action( 'action_bar_before', 'action_bar_generate_form_register_button', 10 );
add_action( 'action_bar_generate_form_register_right', 'action_bar_generate_form_register_button_right', 10 );