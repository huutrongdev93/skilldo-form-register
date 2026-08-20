<?php
namespace FormRegister\Services;

use SkillDo\Cms\Template\Assets\AssetPosition;

class FormRegisterAdminService
{
    static function web(AssetPosition $header, AssetPosition $footer): void
    {
        $footer->add('generate_form_register', asset('generate-form-register::js/form-register-script.js'), ['minify' => true]);
    }

    /**
     * Xoá luôn metadata của các dòng vừa bị xoá.
     *
     * `ActionAjax::delete()` chỉ gọi `$model::whereKey($ids)->delete()` — Eloquent của CMS không
     * đụng tới bảng `<table>_metadata`. Các form có ô phụ (ngày/giờ/dịch vụ… khai ở nhánh
     * `metadata`) vì thế để lại dòng mồ côi trong `form_register_result_metadata`; id sau này
     * trùng lại thì dữ liệu cũ hiện lên dòng mới.
     *
     * Chạy ở hook *_before_success để `$ids` vẫn còn khi lấy.
     */
    public static function deleteMetadata($ids): void
    {
        $ids = array_filter(array_map('intval', \Illuminate\Support\Arr::wrap($ids)));

        if(empty($ids)) return;

        // ⚠ Metadata::delete() chặn ngay ở đầu bằng `is_numeric($object_id)` — truyền MẢNG là nó
        //   lặng lẽ trả false và không xoá gì. Phải gọi từng id một.
        foreach ($ids as $id)
        {
            \FormRegister\Models\FormRegisterResult::deleteMeta($id);
        }
    }

    public static function breadcrumb(): void
    {
        app('breadcrumb.admin')->add('admin.formRegister.index', [
            ['label' => trans('admin::navigation.system'), 'url' => route('admin.system.index')],
            ['label' => 'Form Đăng ký']
        ]);

        app('breadcrumb.admin')->add('admin.formRegister.add', [
            ['label' => trans('admin::navigation.system'), 'url' => route('admin.system.index')],
            ['label' => 'Form Đăng ký', 'url' => route('admin.formRegister.index')],
            ['label' => trans('admin::general.add')]
        ]);

        app('breadcrumb.admin')->add('admin.formRegister.edit', [
            ['label' => trans('admin::navigation.system'), 'url' => route('admin.system.index')],
            ['label' => 'Form Đăng ký', 'url' => route('admin.formRegister.index')],
            ['label' => trans('admin::general.update')]
        ]);

        app('breadcrumb.admin')->add('admin.formRegister.sample', [
            ['label' => trans('admin::navigation.system'), 'url' => route('admin.system.index')],
            ['label' => 'Form Đăng ký', 'url' => route('admin.formRegister.index')],
            ['label' => 'Thêm nhanh']
        ]);
    }
}