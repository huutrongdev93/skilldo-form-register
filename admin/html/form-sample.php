<div class="clearfix"></div>
<div class="wheel-box">
    <?php foreach ($forms as $key => $form) { $form = (object)$form; ?>
        <div class="box item-form js_item_form_sample" data-form-id="<?php echo $form->key?>">
            <div class="header" style="padding:0 10px;margin-bottom:10px;"> <h2><?php echo $form->name?></h2> </div>
            <div class="box-content">
                <div class="m-2">
                    <div class="row wheel-count">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Form key</label>
                                <input type="text" class="form-control" name="key" value="<?php echo $form->key?>">
                            </div>
                            <div class="form-group">
                                <label for="">Form name</label>
                                <input type="text" class="form-control" name="name" value="<?php echo $form->name?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Taxonomy key</label>
                                <input type="text" class="form-control" name="taxonomy" value="<?php echo $form->taxonomy?>">
                            </div>
                            <div class="form-group">
                                <label for="">Taxonomy config</label>
                                <input type="text" class="form-control" name="taxonomy_config" value="<?php echo $form->taxonomy_config?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">List Field</label>
                                <textarea name="field" class="form-control" rows="5"><?php echo $form->field?></textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="">Input name</label>
                            <?php echo $form->sample;?>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-green btn-block js_form_btn_add"><i class="fad fa-plus"></i> Tạo Form</button>
                        </div>
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
        color: #2177B2; font-size:18px; font-weight:bold;
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
<script defer>
    $(function(){
        $('.action-bar .pull-left a').attr('href', base + 'system/generate_form_register');
        $('.js_form_btn_add').click(function() {

            let box = $(this).closest('.js_item_form_sample');

            let data = $( ':input', box ).serializeJSON();

            data.system_tab_key = $('input[name="system_tab_key"]').val();

            data.action     =  'ajax_system_save';

            let load = $(this).find('.loading');

            load.show();

            $.post(ajax, data, function() {}, 'json').done(function(data) {
                load.hide();
                show_message(data.message, data.status);
            });
            return false;
        });
    })
</script>