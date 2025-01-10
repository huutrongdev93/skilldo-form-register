<?php
namespace FormRegister\Table;
use Qr;
use SkillDo\Table\SKDObjectTable;
use Url;

class AdminForm extends SKDObjectTable {

    protected string $module = 'form_register'; //Tên module

    protected mixed $model = \FormRegister\Model\Form::class; //class model

    function getColumns() {

        $this->_column_headers = [
            'cb'       => 'cb',
        ];

        $this->_column_headers['name'] = [
            'label' => trans('Tên form'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('name', $item, $args)
        ];

        $this->_column_headers['key'] = [
            'label' => trans('Key form'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('key', $item, $args)
        ];

        $this->_column_headers['sendMail'] = [
            'label' => trans('Gửi mail'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnBadge::make('send_email', $item, $args)
            ->color(function ($status)
            {
                return ($status == 0) ? 'red' : 'green';
            })
            ->label(function ($status)
            {
                return ($status == 0) ? 'Tắt' : 'Bật';
            })
        ];

        if(\Plugin::isActive('telegram'))
        {
            $this->_column_headers['sendTelegram'] = [
                'label' => trans('Gửi Telegram'),
                'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnBadge::make('send_telegram', $item, $args)
                    ->color(function ($status)
                    {
                        return ($status == 0) ? 'red' : 'green';
                    })
                    ->label(function ($status)
                    {
                        return ($status == 0) ? 'Tắt' : 'Bật';
                    })
            ];
        }

        $this->_column_headers['action']    = trans('table.action');

        $this->_column_headers = apply_filters( "manage_form_register_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {
        $listButton = [];

        $listButton['edit'] = \Admin::button('blue', [
            'href' => Url::admin('system/generate_form_register?view=edit&id='.$item->id),
            'icon' => '<i class="fad fa-cog"></i>',
        ]);
        /**
         * @since 7.0.0
         */
        $listButton = apply_filters('table_form_register_columns_action', $listButton, $item);

        $listButton['delete'] = \Admin::btnDelete([
            'id' => $item->id,
            'model' => 'Form_Register',
        ]);

        return $listButton;
    }

    public function queryFilter(Qr $query, \SkillDo\Http\Request $request): Qr
    {
        return $query;
    }
}