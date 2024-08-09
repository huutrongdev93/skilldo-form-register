<div class="clearfix"></div>
<div class="wheel-box">
   @foreach ($forms as $key => $form)
        <div class="box mb-2 item-form tr_{{$form->id}}" data-form-id="{{$form->id}}">
            <div class="box-header"> <h4 class="box-title">{{$form->name}}</h4> </div>
            <div class="box-content" style="padding: 10px;">
                <div class="wheel-count row">
                    <div class="col-md-2 text-center">
                        <div class="wheel-shadow">
                            <h5>TRẠNG THÁI</h5>
                            <p>{{$form->is_live ? __('SỬ DỤNG') : __('TẮT')}}</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="wheel-shadow">
                            <h5>Gửi email</h5>
                            <p>{{$form->send_email ? __('Bật') : __('tắt')}}</p>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="wheel-shadow">
                            <h5>Chuyển hướng</h5>
                            <p>{{$form->url_redirect ? __('Bật') : __('Tắt')}}</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="wheel-shadow wheel-count">
                            <h5>FILETES</h5>
                            <p style="font-size:14px; margin-bottom:3px;">generate_form_register_{{$form->key}}_error</p>
                            <p style="font-size:14px; margin-bottom:3px;">generate_form_register_{{$form->key}}_data</p>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <a class="btn btn-blue btn-block" href="{!! Url::admin('system/generate_form_register?view=edit&id='.$form->id) !!}"><i class="fad fa-cog"></i></a>
                        {!! Admin::btnDelete(['model' => 'Form_Register', 'id' => $form->id]) !!}
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