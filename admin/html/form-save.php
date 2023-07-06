<div class="col-md-12">
    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="nav-item">
                <a class="nav-link active" href="#base" aria-bs-controls="base" role="tab" data-bs-toggle="tab">CƠ BẢN</a>
            </li>
            <li role="presentation" class="nav-item">
                <a class="nav-link" href="#form" aria-bs-controls="form" role="tab" data-bs-toggle="tab">FORM</a>
            </li>
            <li role="presentation" class="nav-item">
                <a class="nav-link" href="#taxonomy" aria-bs-controls="taxonomy" role="tab" data-bs-toggle="tab">TAXONOMY</a>
            </li>
            <li role="presentation" class="nav-item">
                <a class="nav-link" href="#email_template" aria-bs-controls="email_template" role="tab" data-bs-toggle="tab">Email Template</a>
            </li>
        </ul>
    </div>
    <?php if(isset($form)) { ?><input type="hidden" name="id" value="<?php echo $form->id;?>"><?php } ?>
    <div class="tab-content">
        <!-- BASE -->
        <div role="tabpanel" class="tab-pane active" id="base">
            <div class="box">
                <div class="box-content" style="padding:15px;">

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3"><label for="">Tên form</label></div>
                        <div class="col-md-9">
                            <?php echo $FormBuilder::render(['field' => 'name', 'type' => 'text', 'after' => '<div>', 'before' => '</div>'], (isset($form))?$form->name:'');?>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3"><label for="">Key form</label></div>
                        <div class="col-md-9">
                            <?php echo $FormBuilder::render(['field' => 'key', 'type' => 'text', 'after' => '<div>', 'before' => '</div>'], (isset($form))?$form->key:'');?>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3">
                            <label for="">Bật/Tắt form</label>
                            <p style="font-size:13px;color:#999;">Cho phép khách hàng đăng ký sử dụng form</p>
                        </div>
                        <div class="col-md-9">
                            <input type="checkbox" name="is_live" id="is_live" class="icheck " value="1" <?php echo (isset($form) && $form->is_live == 1) ? 'checked' : '';?>>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3">
                            <label for="">Gửi email</label>
                            <p style="font-size:13px; color:#999;">Bạn cần cấu hình email template để gửi được email theo ý muốn</p>
                        </div>
                        <div class="col-md-9">
                            <input type="checkbox" name="send_email" id="send_email" class="icheck " value="1" <?php echo (isset($form) && $form->send_email == 1) ? 'checked' : '';?>>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3">
                            <label for="">Chuyển hướng sau khi submit</label>
                            <p style="font-size:13px; color:#999;">Sau khi đăng ký thành công trang sẽ được chuyển hướng đi</p>
                        </div>
                        <div class="col-md-9">
                            <input type="checkbox" name="is_redirect" id="is_redirect" class="icheck " value="1" <?php echo (isset($form) && $form->is_redirect == 1) ? 'checked' : '';?>>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3"><label for="">Liên kết chuyển hướng</label></div>
                        <div class="col-md-9">
                            <?php echo FormBuilder::render(['field' => 'url_redirect', 'type' => 'text', 'after' => '<div>', 'before' => '</div>'], (isset($form))?$form->url_redirect:'');?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- /BASE -->

        <!-- BASE -->
        <div role="tabpanel" class="tab-pane" id="form">
            <div class="box">
                <div class="box-content" style="padding:15px;">
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3">
                            <label for="">Field sử dụng</label>
                            <p style="font-size:12px; color:#777"><b>Structure:</b> Input name|post field|label form|type|show in table|rule1,rule2</p>
                            <p style="font-size:12px; color:#777"><b>Rule support:</b> required,email</p>
                            <p style="font-size:12px; color:#777"><b>Type:</b> data or metadata</p>
                        </div>
                        <div class="col-md-9">
                            <?php echo FormBuilder::render(['field' => 'field', 'type' => 'textarea', 'after' => '<div>', 'before' => '</div>'],(isset($form))?$form->field:'');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /BASE -->

        <!-- BASE -->
        <div role="tabpanel" class="tab-pane" id="taxonomy">
            <div class="box">
                <div class="box-content" style="padding:15px;">
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3"><label for="">Taxonomy key</label></div>
                        <div class="col-md-9">
                            <?php echo FormBuilder::render(['field' => 'taxonomy', 'type' => 'text', 'after' => '<div>', 'before' => '</div>'],(isset($form))?$form->taxonomy:'');?>
                        </div>
                    </div>

                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-3"><label for="">Taxonomy config</label></div>
                        <div class="col-md-9">
                            <?php echo FormBuilder::render(['field' => 'taxonomy_config', 'type' => 'textarea', 'after' => '<div>', 'before' => '</div>'],(isset($form))?$form->taxonomy_config:'');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /BASE -->

        <div role="tabpanel" class="tab-pane" id="email_template">
            <div class="box">
                <div class="box-content" style="padding:15px;">
                    <div class="row" style="margin-bottom:10px;">
                        <div class="col-md-12">
                            <p>Biến thay thế dạng {{email}}</p>
                            <?php echo FormBuilder::render(['field' => 'email_template', 'type' => 'wysiwyg', 'after' => '<div>', 'before' => '</div>'],(isset($form))?$form->email_template:'');?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs > li {
        padding:0;
    }
    .nav-tabs > li > a {
        padding:10px; border-radius: 5px; overflow: hidden;
    }
    .action-bar button[form="system_form"] {
        display:none;
    }
</style>
<script defer>
    $(function(){
        $('.action-bar .pull-left a').attr('href', base + 'system/generate_form_register');
    })
</script>