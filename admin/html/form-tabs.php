<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-content" style="padding:20px 10px;">
                <div class="float-start">
                    <a href="<?php echo Url::admin('system/generate_form_register?view=list');?>" class="btn <?php echo ($view == 'list') ? 'btn-blue' : 'btn-white';?> btn-icon"><i class="fa-thin fa-list-ul"></i> Danh sách</a>
                    <a href="<?php echo Url::admin('system/generate_form_register?view=sample');?>" class="btn <?php echo ($view == 'sample') ? 'btn-blue' : 'btn-white';?> btn-icon"><i class="fa-thin fa-plus"></i> Tạo Form nhanh</a>
                </div>
                <div class="float-end">
                    <?php if($view == 'add' || $view == 'edit') { ?>
                        <button type="submit" class="btn-icon btn-green" form="system_form"><?php echo Admin::icon('save');?> Lưu</button>
                    <?php } ?>
                    <?php if($view == 'list' || $view == 'edit') { ?>
                    <a href="<?php echo Url::admin('system/generate_form_register?view=add');?>" class="btn-icon btn-green"><?php echo Admin::icon('add');?> Thêm Mới</a>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>