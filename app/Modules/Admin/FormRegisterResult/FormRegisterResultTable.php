<?php
namespace FormRegister\Modules\Admin\FormRegisterResult;

use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;
use Illuminate\Support\Str;

class FormRegisterResultTable extends SKDObjectTable {

    protected string $module = 'form_register_result';

    protected mixed $model = \FormRegister\Models\FormRegisterResult::class;

    function getColumns()
    {
        $this->_column_headers = [
            'cb'        => 'cb',
            'name'     => [
                'label' => trans('Họ và tên'),
                'column' => fn ($item, $args) => ColumnText::make('name', $item, $args)
            ],
            'email'     => [
                'label' => trans('general.email'),
                'column' => fn ($item, $args) => ColumnText::make('email', $item, $args)
            ],
            'phone' => [
                'label' => trans('general.phone'),
                'column' => fn ($item, $args) => ColumnText::make('phone', $item, $args)
            ],
            'message' => [
                'label' => trans('Ghi chú'),
                'column' => fn ($item, $args) => ColumnText::make('message', $item, $args)
            ],
            'created' => trans('table.created'),
            'action' => trans('table.action')
        ];

        $formKey = request()->input('form-key');

        $this->_column_headers = apply_filters( "manage_form_register_result_".$formKey."_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $buttons = apply_filters('table_form_register_result_'.$item->form_key.'_columns_action', [], $item);

        $deleteTitle = $item->name ?? $item->email ?? $item->phone ?? '#'.$item->id;

        /*
        | ⚠ `module` là BẮT BUỘC. `Admin\Ajax\ActionAjax::delete()` chặn ngay ở đầu:
        |   if (empty($module)) response()->error(trans('ajax.delete.noModule'));
        | Thiếu nó thì bấm xoá chỉ nhận được "Bạn vui lòng cung cấp module cần xóa" — không có
        | dòng nào bị xoá và thông báo không hề nhắc tới nút hay bảng nào. Mọi bảng của core
        | (Page/Post/Tag) đều truyền `'module' => $this->module`.
        */
        $buttons[] = Admin::btnDelete([
            'id' => $item->id,
            'model' => $this->model,
            'module' => $this->module,
            'description' => trans('admin::message.page.confirmDelete', ['title' => html_escape((string)$deleteTitle)])
        ]);

        return apply_filters('table_form_register_result_columns_action', $buttons, $item);
    }

    /**
     * Cột `cb` vẽ sẵn ô chọn cho từng dòng nhưng bảng này chưa khai hành động hàng loạt nào,
     * nên chọn xong không có gì để bấm. Khai xoá hàng loạt cho khớp với các bảng của core.
     */
    public function bulkAction(): array
    {
        return [
            'formRegisterResultDelete' => [
                'icon'  => Admin::icon('delete'),
                'label' => trans('generate-form-register::register.result.delete.label'),
                'class' => 'js_btn_confirm',
                'attributes' => [
                    'data-action'      => 'delete',
                    // ⚠ Dùng ĐÚNG tên đã đăng ký ở views/admin/bootstrap/ajax.php.
                    //   Các bảng của core còn ghi 'Ajax_Admin_Action::delete' — tên cũ thời v6,
                    //   không có trong dispatcher nữa.
                    'data-ajax'        => 'Admin\Ajax\ActionAjax::delete',
                    'data-module'      => $this->module,
                    'data-model'       => $this->model,
                    'data-heading'     => trans('generate-form-register::register.result.delete.heading'),
                    'data-description' => trans('generate-form-register::register.result.delete.description'),
                ]
            ]
        ];
    }

    function headerFilter(Form $form, Request $request)
    {
        $formKey  = $request->input('form-key');

        $form->hidden('form-key', [], Str::clear($formKey));

        /**
         * @singe v7.0.0
         */
        return apply_filters('admin_form_register_result_table_form_filter', $form, $formKey);
    }

    function headerSearch(Form $form, Request $request): Form
    {
        $formKey  = $request->input('form-key');

        $form->daterange('time', [
            'placeholder' => 'Thời gian đăng ký',
        ]);

        /**
         * @singe v7.0.0
         */
        return apply_filters('admin_register_form_result_table_form_search', $form, $formKey);
    }

    function headerButton(): array
    {
        $buttons['export'] = Admin::button('blue', [
            'id' => 'js_export_form_register_result_btn_modal',
            'icon' => '<i class="fa-light fa-download"></i>',
            'text' => trans('generate-form-register::export.data')
        ]);

        $buttons['reload'] = Admin::button('reload');

        return $buttons;
    }

    public function queryFilter(Builder $query, Request $request): Builder
    {
        $formKey = $request->input('form-key');

        $query->where('form_key', $formKey);

        $time = Str::clear($request->input('time'));

        if(!empty($time))
        {
            $time = explode(' - ', $time);

            if(hasItems($time) && count($time) == 2)
            {
                $time[0] = str_replace('/', '-', $time[0]);
                $time[1] = str_replace('/', '-', $time[1]);
                $timeStart = date('Y-m-d', strtotime($time[0])).' 00:00:00';
                $timeEnd   = date('Y-m-d', strtotime($time[1])).' 23:59:59';
                $query->where('created', '>=', $timeStart);
                $query->where('created', '<=', $timeEnd);
            }
        }

        return $query;
    }

    public function queryDisplay(Builder $query, Request $request, $data = []): Builder
    {
        $query = parent::queryDisplay($query, $request, $data);

        $query->orderBy('created', 'desc');

        return $query;
    }
}