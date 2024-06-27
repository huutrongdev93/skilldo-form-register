<form method="post" id="system_form">
    {!! Admin::loading() !!}
    <div class="col-md-12">
        <div role="tabpanel">
            <ul class="nav nav-tabs nav-tabs-horizontal mb-3" role="tablist">
                <li role="presentation" class="nav-item">
                    <a class="nav-link active" href="#base" aria-bs-controls="base" role="tab" data-bs-toggle="tab">CƠ BẢN</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link" href="#form" aria-bs-controls="form" role="tab" data-bs-toggle="tab">FORM</a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link" href="#email_template" aria-bs-controls="email_template" role="tab" data-bs-toggle="tab">Email Template</a>
                </li>
            </ul>
        </div>
        @if(isset($form))
            <input type="hidden" name="id" value="{!! $form->id !!}">
        @endif
        <div class="tab-content">
            <!-- BASE -->
            <div role="tabpanel" class="tab-pane active" id="base">
                <div class="box">
                    <div class="box-content" style="padding:15px;">

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3"><label for="">Tên form</label></div>
                            <div class="col-md-9">
                                {!! \SkillDo\Form\Form::render(['field' => 'name', 'type' => 'text', 'start' => '<div>', 'end' => '</div>'], (isset($form)) ? $form->name :'') !!}
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3"><label for="">Key form</label></div>
                            <div class="col-md-9">
                                {!! \SkillDo\Form\Form::render(['field' => 'key', 'type' => 'text', 'start' => '<div>', 'end' => '</div>'], (isset($form)) ? $form->key : '') !!}
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3">
                                <label for="">Bật/Tắt form</label>
                                <p style="font-size:13px;color:#999;">Cho phép khách hàng đăng ký sử dụng form</p>
                            </div>
                            <div class="col-md-9">
                                <input type="checkbox" name="is_live" id="is_live" class="icheck " value="1" {!! (isset($form) && $form->is_live == 1) ? 'checked' : '' !!}>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3">
                                <label for="">Gửi email</label>
                                <p style="font-size:13px; color:#999;">Bạn cần cấu hình email template để gửi được email theo ý muốn</p>
                            </div>
                            <div class="col-md-9">
                                <input type="checkbox" name="send_email" id="send_email" class="icheck " value="1" {!! (isset($form) && $form->send_email == 1) ? 'checked' : '' !!}>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3">
                                <label for="">Chuyển hướng sau khi submit</label>
                                <p style="font-size:13px; color:#999;">Sau khi đăng ký thành công trang sẽ được chuyển hướng đi</p>
                            </div>
                            <div class="col-md-9">
                                <input type="checkbox" name="is_redirect" id="is_redirect" class="icheck " value="1" {!! (isset($form) && $form->is_redirect == 1) ? 'checked' : '' !!}>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px;">
                            <div class="col-md-3"><label for="">Liên kết chuyển hướng</label></div>
                            <div class="col-md-9">
                                {!! \SkillDo\Form\Form::render(['field' => 'url_redirect', 'type' => 'text', 'start' => '<div>', 'end' => '</div>'], (isset($form)) ? $form->url_redirect : '') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /BASE -->

            <!-- BASE -->
            <div role="tabpanel" class="tab-pane" id="form">
                <div class="box mb-2">
                    <div class="box-header"><h4 class="box-title">Trường mặc định</h4></div>
                    <div class="box-content">
                        <div class="row">
                            {!! $formDefault->html() !!}
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header"><h4 class="box-title">Trường tùy biến</h4></div>
                    <div class="box-content">
                        <div class="row">
                            {!! $formMeta->html() !!}
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
                                <p>Biến thay thế dạng {{$email}}</p>
                                {!! \SkillDo\Form\Form::render(['field' => 'email_template', 'type' => 'wysiwyg', 'after' => '<div>', 'before' => '</div>'],(isset($form)) ? $form->email_template : '') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .nav-tabs > li {
        padding:0;
    }
    .nav-tabs > li > a {
        padding:10px;
        border-radius: 5px;
        overflow: hidden;
    }
</style>

<script defer>
	$(function(){

		$('#system_form').submit(function() {

			let data = $(this).serializeJSON();

			data.action     =  'Form_Register_Ajax::adminSave';

			let load = $(this).find('.loading');

			load.show();

			request.post(ajax, data).then(function(response) {

				load.hide();

				SkilldoMessage.response(response);
			});

			return false;
		});
	})
</script>