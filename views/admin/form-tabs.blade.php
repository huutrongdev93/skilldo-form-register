<div class="row">
    <div class="col-md-12">
        <div class="box mb-2">
            <div class="box-content" style="padding:20px 10px;">
                <div class="float-start">
                    <a href="{!! route('admin.formRegister.index') !!}" class="btn"><i class="fa-thin fa-list-ul"></i> Danh sách</a>
                    <a href="{!! route('admin.formRegister.sample') !!}" class="btn"><i class="fa-thin fa-plus"></i> Tạo Form nhanh</a>
                </div>
                <div class="float-end">
                    <button type="submit" class="btn btn-green" form="system_form">
                        {!! Admin::icon('save') !!} Lưu
                    </button>
                    <a href="{!! route('admin.formRegister.add') !!}" class="btn btn-green">
                        {!! Admin::icon('add') !!} Thêm Mới
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>