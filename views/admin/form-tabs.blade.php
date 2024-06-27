<div class="row">
    <div class="col-md-12">
        <div class="box mb-2">
            <div class="box-content" style="padding:20px 10px;">
                <div class="float-start">
                    <a href="{!! Url::admin('system/generate_form_register?view=list') !!}" class="btn {{($view == 'list') ? 'btn-blue' : 'btn-white'}} btn-icon"><i class="fa-thin fa-list-ul"></i> Danh sách</a>
                    <a href="{!! Url::admin('system/generate_form_register?view=sample') !!}" class="btn {{($view == 'sample') ? 'btn-blue' : 'btn-white'}} btn-icon"><i class="fa-thin fa-plus"></i> Tạo Form nhanh</a>
                </div>
                <div class="float-end">
                    @if($view == 'add' || $view == 'edit')
                        <button type="submit" class="btn btn-green" form="system_form">
                            {!! Admin::icon('save') !!} Lưu
                        </button>
                    @endif
                    @if($view == 'list' || $view == 'edit')
                        <a href="{!! Url::admin('system/generate_form_register?view=add') !!}" class="btn btn-green">
                            {!! Admin::icon('add') !!} Thêm Mới
                        </a>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>