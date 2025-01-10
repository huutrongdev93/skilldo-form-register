<?php
namespace FormRegister\Table;
use Qr;
use SkillDo\Form\Form;
use SkillDo\Http\Request;
use SkillDo\Table\SKDObjectTable;
use Str;

class AdminResult extends SKDObjectTable {

    protected string $module = 'form_register_result'; //Tên module

    protected mixed $model = \FormRegister\Model\FormResult::class; //class model

    function getColumns() {

        $this->_column_headers = [
            'cb'       => 'cb',
        ];

        $this->_column_headers['name'] = [
            'label' => trans('Họ và tên'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('name', $item, $args)
        ];

        $this->_column_headers['email'] = [
            'label' => trans('general.email'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('email', $item, $args)
        ];

        $this->_column_headers['phone'] = [
            'label' => trans('general.phone'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('phone', $item, $args)
        ];

        $this->_column_headers['message'] = [
            'label' => trans('Ghi chú'),
            'column' => fn ($item, $args) => \SkillDo\Table\Columns\ColumnText::make('message', $item, $args)
        ];

        $this->_column_headers['created'] = trans('table.created');

        $formKey = request()->input('form-key');

        $this->_column_headers['action']    = trans('table.action');

        $this->_column_headers = apply_filters( "manage_form_register_result_".$formKey."_columns", $this->_column_headers );

        return $this->_column_headers;
    }

    function actionButton($item, $module, $table): array
    {

        $listButton = [];

        /**
         * @since 7.0.0
         */
        $listButton = apply_filters('table_form_register_result_'.$item->form_key.'_columns_action', $listButton, $item);

        $listButton['delete'] = \Admin::btnDelete([
            'id' => $item->id,
            'model' => $module,
        ]);

        return $listButton;
    }

    function headerFilter(Form $form, Request $request)
    {
        $formKey  = $request->input('form-key');

        $form->hidden('form-key', [], \Str::clear($formKey));

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

    public function queryFilter(Qr $query, \SkillDo\Http\Request $request): Qr
    {
        $formKey = $request->input('form-key');

        $query->where('form_key', $formKey);

        $time = Str::clear($request->input('time'));

        if(!empty($time)) {
            $time = explode(' - ', $time);
            if(have_posts($time) && count($time) == 2) {
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

    public function queryDisplay(Qr $query, \SkillDo\Http\Request $request, $data = []): Qr
    {
        $query = parent::queryDisplay($query, $request, $data);

        $query->orderBy('created', 'desc');

        return $query;
    }
}