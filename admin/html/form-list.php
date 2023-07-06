<div class="clearfix"></div>
<div class="wheel-box">
    <?php foreach ($forms as $key => $form) {?>
        <div class="box item-form tr_<?php echo $form->id?>" data-form-id="<?php echo $form->id?>">
            <div class="header" style="padding:0px 10px;"> <h2><?php echo $form->name?></h2> </div>
            <div class="box-content" style="padding: 10px;">
                <div class="wheel-count row">
                    <div class="col-md-2 text-center">
                        <div class="wheel-shadow">
                            <h5>TRẠNG THÁI</h5>
                            <p><?php echo $form->is_live ? __('SỬ DỤNG') : __('TẮT'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="wheel-shadow">
                            <h5>Gửi email</h5>
                            <p><?php echo $form->send_email ? __('SỬ DỤNG') : __('TẮT'); ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="wheel-shadow">
                            <h5>TAXONOMY</h5>
                            <p><?php echo $form->taxonomy;?></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="wheel-shadow wheel-count">
                            <h5>FILETES</h5>
                            <p style="font-size:14px; margin-bottom:3px;">generate_form_register_<?php echo $form->key;?>_error</p>
                            <p style="font-size:14px; margin-bottom:3px;">generate_form_register_<?php echo $form->key;?>_data</p>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-blue btn-block" href="<?php echo Url::admin('system/generate_form_register?view=edit&id='.$form->id);?>"><i class="fad fa-cog"></i></a>
                        <?php echo Admin::btnDelete(['module' => 'Form_Register', 'id' => $form->id, 'style' => 'display:block;width:100%;margin-top:10px;']);?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<style>
    .item-form .wheel-count h5 {
        color: rgb(128,128,128); font-size:14px;
    }
    .item-form .wheel-count p {
        color: #2177B2; font-size:23px; font-weight:bold;
    }
    .item-form .wheel-shadow {
        box-shadow: 0px 0px 5px #d2cfcf;
        border-radius: 5px;
        padding: 10px;
    }
    .action-bar button[form="system_form"] {
        display:none;
    }
</style>