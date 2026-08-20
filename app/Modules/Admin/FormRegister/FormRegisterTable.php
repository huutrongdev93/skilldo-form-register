<?php
namespace FormRegister\Modules\Admin\FormRegister;

use SkillDo\Cms\Form\Form;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Table\Columns\ColumnBadge;
use SkillDo\Cms\Table\Columns\ColumnEdit;
use SkillDo\Cms\Table\Columns\ColumnText;
use SkillDo\Cms\Table\SKDObjectTable;
use SkillDo\Database\Eloquent\Builder;
use SkillDo\Http\Request;

class FormRegisterTable extends SKDObjectTable {

    protected string $module = 'form_register';

    protected mixed $model = \FormRegister\Models\FormRegister::class;

    function getColumns()
    {
        $this->_column_headers = [
            'cb'        => 'cb',
            'name'     => [
                'label' => trans('Tên form'),
                'column' => fn ($item, $args) => ColumnText::make('name', $item, $args)
            ],
            'key'     => [
                'label' => trans('Key form'),
                'column' => fn ($item, $args) => ColumnText::make('key', $item, $args)
            ],
            'sendMail'     => [
                'label' => trans('Gửi mail'),
                'column' => fn ($item, $args) => ColumnBadge::make('send_email', $item, $args)
                    ->color(function ($status)
                    {
                        return ($status == 0) ? 'red' : 'green';
                    })
                    ->label(function ($status)
                    {
                        return ($status == 0) ? 'Tắt' : 'Bật';
                    })
            ],
            'action' => trans('table.action')
        ];

        $this->_column_headers = apply_filters( "manage_form_register_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $buttons[] = Admin::button('blue', [
            'href' => route('admin.formRegister.edit', [$item->id]),
            'icon' => Admin::icon('edit')
        ]);

        $buttons = apply_filters('table_form_register_columns_action', $buttons, $item);

        $buttons[] = Admin::btnDelete([
            'id' => $item->id,
            'model' => $this->model,
            'module' => $this->module,
            'description' => trans('admin::message.page.confirmDelete', ['title' => html_escape($item->name)])
        ]);

        return apply_filters('table_form_register_columns_action', $buttons, $item);
    }

    function headerButton(): array
    {
        $buttons['add'] = Admin::button('add', [
            'href' => route('admin.formRegister.add'),
            'text' => trans('button.add')
        ]);

        $buttons['add-speed'] = Admin::button('blue', [
            'href' => route('admin.formRegister.sample'),
            'icon' => Admin::icon('add'),
            'text' => 'Thêm nhanh'
        ]);

        $buttons['reload'] = Admin::button('reload');

        return $buttons;
    }
}