<div class="clearfix"></div>
<div class="wheel-box">
    @foreach ($dataFormSample as $key => $form)
        <div class="box mb-2 item-form js_item_form_sample" data-form-id="{!! $form->key !!}">
            <div class="box-header"> <h4 class="box-title">{!! $form->name !!}</h4> </div>
            <div class="box-content">
                <div class="m-2">
                    <div class="row wheel-count">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Form key</label>
                                <input type="text" class="form-control" name="key" value="{!! $form->key !!}">
                            </div>
                            <div class="form-group">
                                <label for="">Form name</label>
                                <input type="text" class="form-control" name="{!! $form->key !!}[name]" value="{!! $form->name !!}">
                            </div>
                            <button class="btn btn-green btn-block js_form_btn_add w-100"><i class="fad fa-plus"></i> Tạo Form</button>
                        </div>
                        <div class="col-md-8">
                            {!! $forms[$key]['default']->html() !!}
                            {!! $forms[$key]['metadata']->html() !!}
                        </div>
                        <div class="col-md-2">
                            <label for="">Input name</label>
                            {!! $form->sample !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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

        $('.js_form_btn_add').click(function() {

			let button = SkilldoUtil.buttonLoading($(this));

            let box = $(this).closest('.js_item_form_sample');

            let data = $( ':input', box ).serializeJSON();

            data.formId = box.attr('data-form-id');

            data.action     =  'Form_Register_Ajax::quickCreate';

            let load = $(this).find('.loading');

            load.show();

	        button.start();

            request.post(ajax, data).then(function(response) {

                load.hide();

	            button.stop();

                SkilldoMessage.response(response);
            });

            return false;
        });
    })
</script>