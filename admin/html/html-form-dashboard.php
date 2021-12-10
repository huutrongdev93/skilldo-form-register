<div class="col-md-12">
    <div class="ui-title-bar__group" style="padding-bottom:5px;">
        <h1 class="ui-title-bar__title">FORM ĐĂNG KÝ</h1>
        <p style="margin-top: 10px; margin-left: 1px; color: #8c8c8c">Tạo các form đăng ký thông tin</p>
        <div class="ui-title-bar__action">
            <a href="<?php echo Url::admin().'plugins?page=generate_form_register';?>" class="active btn btn-default"><i class="fas fa-comment-dollar"></i> Danh sách</a>
            <a href="<?php echo Url::admin().'plugins?page=generate_form_register&tab=sample';?>" class=" btn btn-default"> <i class="fal fa-shipping-fast"></i> Tạo Form nhanh</a>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<br />
<?php
if(empty($tab)) {
    if($action == 'add_form') {
        include 'html-form-save.php';
    }
    else if($action == 'edit_form') {
        include 'html-form-save.php';
    }
    else {
        include 'html-form-index.php';
    }
}
if($tab == 'sample') {
    include 'html-sample-index.php';
}