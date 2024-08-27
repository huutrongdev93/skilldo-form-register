<?php
use JetBrains\PhpStorm\NoReturn;
use SkillDo\Validate\Rule;
use SkillDo\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Class Form_Register_Ajax {
    #[NoReturn]
    static function register(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $form_key = $request->input('form_key');

            $form 	  = Form_Register::where('key', $form_key)->first();

            $post 	  = $request->input();

            unset($post['action']);

            if(have_posts($form) && have_posts($post)) {

                if($form->is_live == 0) {
                    response()->error(trans('register.gfr.off'));
                }

                $fields = unserialize($form->field);

                if(have_posts($fields)) {

                    $errors = '';

                    $errors = apply_filters('generate_form_register_'.$form_key.'_error', $errors, $form );

                    if(is_skd_error($errors)) {

                        response()->error($errors);
                    }

                    $data 		= [];

                    $metadata 	= [];

                    $validations = [];

                    foreach ($fields as $inputTypes) {

                        foreach ($inputTypes as $input) {

                            if (isset($input['use']) && $input['use'] == 0) continue;

                            $validation = Rule::make($input['label']);

                            if (!empty($input['required'])) {
                                $validation->notEmpty();
                            }

                            if (!empty($input['isEmail'])) {
                                $validation->email();
                            }

                            if (!empty($input['isPhone'])) {
                                $validation->phone();
                            }

                            if (!empty($validation->validators())) {
                                $validations[$input['field']] = $validation;
                            }
                        }
                    }

                    if(!empty($validations)) {

                        $validate = $request->validate($validations)->validate();

                        if ($validate->fails()) {
                            response()->error($validate->errors());
                        }
                    }

                    foreach ($fields['default'] as $column => $input) {

                        $data[$column] = $request->input($input['field']);

                        if(is_string($data[$column])) {
                            $data[$column] = Str::clear($data[$column]);
                        }
                    }

                    foreach ($fields['metadata'] as $column => $input) {

                        $metadata[$input['name']] = $request->input($input['field']);

                        if(is_string($metadata[$input['name']])) {
                            $metadata[$input['name']] = Str::clear($metadata[$input['name']]);
                        }
                    }

                    $data['status'] 	= 1;

                    $data['form_key'] = $form->key;

                    $data = apply_filters('generate_form_register_'.$form_key.'_data', $data, $form);

                    $res  = Form_Register_Result::insert($data);

                    if(!is_skd_error($res)) {

                        \SkillDo\Cache::delete('generate_form_count_'.$form->key);

                        if(have_posts($metadata)) {
                            foreach ($metadata as $meta_key => $meta_value) {
                                Form_Register_Result::updateMeta($res, $meta_key, $meta_value);
                            }
                        }

                        if($form->send_email == 1) {

                            $subject = 'Có email được gửi từ form '.$form->name.' vào '.date('d/m/Y H:i');

                            $name = (isset($data['name'])) ? $data['name'] : 'No name';

                            $content = file_get_contents(FCPATH.Path::plugin('generate-form-register').'/views/email/template-1.php');

                            $content = str_replace('{{email_template}}', $form->email_template, $content);

                            $data['base_url'] = Url::base();

                            $dataEmail = apply_filters('generate_form_register_'.$form_key.'_email_data',[...$data, ...$metadata], $form);

                            Mail::to(Option::get('contact_mail'))
                                ->subject($subject)
                                ->replyTo(Option::get('contact_mail'), $name)
                                ->body($content, $dataEmail)
                                ->send();
                        }

                        $result['data'] 	    = [];

                        $result['is_redirect'] 	= false;

                        if($form->is_redirect == 1 && !empty($form->url_redirect)) {

                            $result['is_redirect'] 	= true;

                            $result['url_redirect'] = $form->url_redirect;
                        }

                        do_action('generate_form_register_success', $form);

                        response()->success(trans('register.gfr.success'), $result);
                    }
                }
            }
        }

        response()->error(trans('register.gfr.fail'));
    }

    #[NoReturn]
    static function adminSave(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $key = $request->input('key');

            $id  = $request->input('id');

            if(empty($key)) {
                response()->error('Key form không được để trống');
            }

            $data = $request->input();

            unset($data['action']);
            unset($data['post_type']);
            unset($data['cate_type']);
            unset($data['csrf_test_name']);

            $field = [
                'default' => [],
                'metadata' => []
            ];

            if(empty($data['fieldName'])) {
                response()->error('Cấu hình cho trường name chưa có');
            }
            if(empty($data['fieldEmail'])) {
                response()->error('Cấu hình cho trường email chưa có');
            }
            if(empty($data['fieldPhone'])) {
                response()->error('Cấu hình cho trường phone chưa có');
            }
            if(empty($data['fieldMessage'])) {
                response()->error('Cấu hình cho trường message chưa có');
            }

            $field['default']['name'] = $data['fieldName'];

            $field['default']['email'] = $data['fieldEmail'];

            $field['default']['phone'] = $data['fieldPhone'];

            $field['default']['message'] = $data['fieldMessage'];

            unset($data['fieldName']);

            unset($data['fieldEmail']);

            unset($data['fieldPhone']);

            unset($data['fieldMessage']);

            if(!empty($data['metaData'])) {
                $field['metadata'] = $data['metaData'];
                unset($data['metaData']);
            }

            $data['field'] = $field;

            //Thêm mới
            if(empty($id)) {

                if(Form_Register::count(Qr::set('key', $key)) != 0) {

                    response()->error('Key form đã tồn tại');
                }

                if(!isset($data['key'])) $data['key'] = $key;

                if(!isset($data['is_live'])) $data['is_live'] = 1;

                $error = Form_Register::insert($data);

                if(is_skd_error($error)) {
                    response()->error($error);
                }

                Form_Register_Helper::build();

                response()->success(trans('ajax.add.success'));
            }
            //Cập nhật
            else {

                $form   = Form_Register::get($id);

                if(!have_posts($form)) {
                    response()->error(trans('Form không tồn tại'));
                }

                $form_data = [
                    'id' => $id,
                ];

                foreach ($data as $key => $value) {
                    if($key  == 'email_template') {
                        $form_data[$key] = $value;
                    }
                    else {
                        if(!have_posts($value)) {
                            $form_data[$key] = trim(Str::clear($value));
                        }
                        else {
                            $form_data[$key] = $value;
                        }
                    }
                }

                if(!isset($form_data['is_live'])) $form_data['is_live'] = 0;

                if(!isset($form_data['send_email'])) $form_data['send_email'] = 0;

                if(!isset($form_data['is_redirect'])) $form_data['is_redirect'] = 0;

                $error = Form_Register::insert($form_data);

                if(is_skd_error($error)) {
                    response()->error($error);
                }

                Form_Register_Helper::build();

                response()->success(trans('ajax.update.success'));
            }
        }

        response()->error(trans('ajax.update.error'));
    }

    #[NoReturn]
    static function quickCreate(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $key = $request->input('key');

            $formId = $request->input('formId');

            if(empty($key)) {
                response()->error('Key form không được để trống');
            }

            if(empty($formId)) {
                response()->error('Id form không được để trống');
            }

            $data = $request->input($formId);

            $field = [
                'default' => [],
                'metadata' => []
            ];

            if(empty($data['fieldName'])) {
                response()->error('Cấu hình cho trường name chưa có');
            }
            if(empty($data['fieldEmail'])) {
                response()->error('Cấu hình cho trường email chưa có');
            }
            if(empty($data['fieldPhone'])) {
                response()->error('Cấu hình cho trường phone chưa có');
            }
            if(empty($data['fieldMessage'])) {
                response()->error('Cấu hình cho trường message chưa có');
            }

            $field['default']['name'] = $data['fieldName'];

            $field['default']['email'] = $data['fieldEmail'];

            $field['default']['phone'] = $data['fieldPhone'];

            $field['default']['message'] = $data['fieldMessage'];

            unset($data['fieldName']);

            unset($data['fieldEmail']);

            unset($data['fieldPhone']);

            unset($data['fieldMessage']);

            if(!empty($data['metaData'])) {
                $field['metadata'] = $data['metaData'];
                unset($data['metaData']);
            }

            $data['field'] = $field;

            if(Form_Register::count(Qr::set('key', $key)) != 0) {

                response()->error('Key form đã tồn tại');
            }

            if(!isset($data['key'])) $data['key'] = $key;

            if(!isset($data['is_live'])) $data['is_live'] = 1;

            $error = Form_Register::insert($data);

            if(is_skd_error($error)) {
                response()->error($error);
            }

            Form_Register_Helper::build();

            response()->success(trans('ajax.add.success'));
        }

        response()->error(trans('ajax.update.error'));
    }

    #[NoReturn]
    static function load(Request $request): void
    {
        if($request->isMethod('post')) {

            $page    = $request->input('page');

            $page   = (is_null($page) || empty($page)) ? 1 : (int)$page;

            $limit  = $request->input('limit');

            $limit   = (is_null($limit) || empty($limit)) ? 10 : (int)$limit;

            $formKey = $request->input('form-key');

            $recordsTotal   = $request->input('recordsTotal');

            $args = Qr::set('form_key', $formKey);

            $time = Str::clear($request->input('time'));

            if(!empty($time)) {
                $time = explode(' - ', $time);
                if(have_posts($time) && count($time) == 2) {
                    $time[0] = str_replace('/', '-', $time[0]);
                    $time[1] = str_replace('/', '-', $time[1]);
                    $timeStart = date('Y-m-d', strtotime($time[0])).' 00:00:00';
                    $timeEnd   = date('Y-m-d', strtotime($time[1])).' 23:59:59';
                    $args->where('created', '>=', $timeStart);
                    $args->where('created', '<=', $timeEnd);
                }
            }

            /**
             * @since 7.0.0
             */
            $args = apply_filters('admin_form_register_result_controllers_index_args_before_count', $args);

            if(!is_numeric($recordsTotal)) {
                $recordsTotal = apply_filters('admin_form_register_result_controllers_index_count', Form_Register_Result::count($args), $args);
            }


            # [List data]
            $args->limit($limit)
                ->offset(($page - 1)*$limit)
                ->orderBy('created', 'desc');

            $args = apply_filters('admin_form_register_result_controllers_index_args', $args);

            $objects = apply_filters('admin_form_register_result_controllers_index_objects', Form_Register_Result::gets($args), $args);

            $args = [
                'items' => $objects,
                'table' => 'form_register_result',
                'model' => model('form_register_result'),
                'module'=> 'form_register_result',
            ];

            $table = new AdminFormRegisterTable($args);
            $table->get_columns();
            ob_start();
            $table->display_rows_or_message();
            $html = ob_get_contents();
            ob_end_clean();

            /**
             * Bulk Actions
             * @hook table_*_bulk_action_buttons Hook mới phiên bản 7.0.0
             */
            $buttonsBulkAction = apply_filters('table_form_register_result_bulk_action_buttons', []);

            $bulkAction = Admin::partial('include/table/header/bulk-action-buttons', [
                'actionList' => $buttonsBulkAction
            ]);

            $result['data'] = [
                'html'          => base64_encode($html),
                'bulkAction'    => base64_encode($bulkAction),
            ];
            $result['pagination']   = [
                'limit' => $limit,
                'total' => $recordsTotal,
                'page'  => (int)$page,
            ];

            response()->success(trans('ajax.load.success'), $result);
        }

        response()->error(trans('ajax.load.error'));
    }

    #[NoReturn]
    static function export(Request $request, $model): void
    {
        if($request->isMethod('post')) {

            $exportType = $request->input('exportType');

            $search = $request->input('search');

            if(empty($search['form-key'])) {
                response()->error(trans('Không xác định được loại form cần xuất'));
            }

            $formKey = trim($search['form-key']);

            $args    = Qr::set('form_key', $formKey);

            if($exportType === 'pageCurrent') {

                $listId = $request->input('listData');

                if(!have_posts($listId)) {
                    response()->error(trans('Không có dữ liệu nào để xuất'));
                }

                $args->whereIn('id', $listId);
            }

            if($exportType === 'check') {

                $listId = $request->input('listData');

                if(!have_posts($listId)) {
                    response()->error(trans('Không có dữ liệu nào để xuất'));
                }

                $args->whereIn('id', $listId);
            }

            if($exportType === 'searchCurrent') {

                if(!empty($search['time'])) {
                    $time = explode(' - ', $search['time']);
                    if(have_posts($time) && count($time) == 2) {
                        $time[0] = str_replace('/', '-', $time[0]);
                        $time[1] = str_replace('/', '-', $time[1]);
                        $timeStart = date('Y-m-d', strtotime($time[0])).' 00:00:00';
                        $timeEnd   = date('Y-m-d', strtotime($time[1])).' 23:59:59';
                        $args->where('created', '>=', $timeStart);
                        $args->where('created', '<=', $timeEnd);
                    }
                }

                # [Total decoders]
                $args = apply_filters('admin_form_register_result_controllers_index_args_count', $args);
            }

            $formResults = Form_Register_Result::gets($args);

            $form = Form_Register::where('key', $formKey)->first();

            $fields = unserialize($form->field);

            $excelCharacters = [
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ',
                'BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ'
            ];

            $spreadsheet = new Spreadsheet();

            $styleHeader = [
                'font' => [ 'bold' => true, 'size' => 12],
                'alignment' => ['horizontal' => 'left', 'vertical'   => 'center'],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ];

            $styleBody = [
                'alignment' => [
                    'vertical' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'E6F7FF',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000'],
                    ],
                ],
            ];

            $headerSheet = [];

            foreach ($fields['default'] as $column => $input) {
                if(empty($input['use'])) continue;
                $headerSheet[$column] = [
                    'label' => $input['label'],
                    'value' => function($item) use ($column) {
                        return $item->{$column};
                    }
                ];
            }

            foreach ($fields['metadata'] as $column => $input) {
                $headerSheet[$input['name']] = [
                    'label' => $input['label'],
                    'value' => function($item) use ($input) {
                        return Form_Register_Result::getMeta($item->id, $input['name'], true);
                    }
                ];
            }

            $headerSheet['created'] = [
                'label' => 'Ngày đăng ký',
                'value' => function($item) {
                    return $item->created;
                },
                'width' => 20
            ];

            $alignment['horizontal'] = [
                'right' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'left'  => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'center' => PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ];

            $alignment['vertical'] = [
                'top'    => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'center' => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ];

            $sheet = $spreadsheet->setActiveSheetIndex(0);

            $sheet->setTitle($form->name);

            $sheet->getDefaultRowDimension()->setRowHeight(20);

            $sheet->getDefaultRowDimension()->setRowHeight(20);

            $key = 0;

            foreach ($headerSheet as $headerKey => $item) {
                $headerSheet[$headerKey]['cell'] =  $excelCharacters[$key].'1';
                if(!empty($item['width'])) {
                    $sheet->getColumnDimension($excelCharacters[$key])->setWidth($item['width']);
                }
                else {
                    $sheet->getColumnDimension($excelCharacters[$key])->setAutoSize(true);
                }
                $key++;
            }

            foreach ($headerSheet as $headerKey => $headerData) {

                $sheet->setCellValue($headerData['cell'], $headerData['label']);

                $style = (isset($headerData['style'])) ? $headerData['style'] : $styleHeader;

                if(isset($style['alignment']['horizontal'])) {
                    $style['alignment']['horizontal'] = $alignment['horizontal'][$style['alignment']['horizontal']];
                }

                if(isset($style['alignment']['vertical'])) {
                    $style['alignment']['vertical'] = $alignment['vertical'][$style['alignment']['vertical']];
                }

                if(!empty($style)) {
                    $sheet->getStyle($headerData['cell'])->applyFromArray($style);
                }
            }

            $rows = [];

            foreach ($formResults as $keyProduct => $item) {
                $i = 0;
                foreach ($headerSheet as $header) {
                    $rows[] = [
                        'cell'  => $excelCharacters[$i] .($keyProduct+2),
                        'value' => $header['value']($item),
                        'style' => $styleBody
                    ];
                    $i++;
                }
            }

            foreach ($rows as $row) {
                $sheet->setCellValue($row['cell'], $row['value']);
                $sheet->getPageMargins()->setTop(2);
                $sheet->getPageMargins()->setRight(2);
                $sheet->getPageMargins()->setLeft(2);
                $sheet->getPageMargins()->setBottom(2);
                $sheet->getStyle($row['cell'])->applyFromArray($row['style']);
            }

            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);

            $filePathData = Path::upload('export/');

            if(!file_exists($filePathData)) {
                mkdir($filePathData, 0755);
                chmod($filePathData, 0755);
            }

            $filename = 'form_'.md5(time()).'_'.date('d-m-Y').'.xlsx';

            $writer->save($filePathData.$filename);

            $path = Url::base().$filePathData.$filename;

            response()->success(trans('ajax.load.success'), $path);
        }

        response()->error(trans('Xuất dữ liệu không thành công'));
    }
}
Ajax::client('Form_Register_Ajax::register');
Ajax::admin('Form_Register_Ajax::adminSave');
Ajax::admin('Form_Register_Ajax::quickCreate');
Ajax::admin('Form_Register_Ajax::load');
Ajax::admin('Form_Register_Ajax::export');

#[NoReturn]
function ajax_email_register($ci, $model): void {
    Form_Register_Ajax::register($ci, $model);
}
Ajax::client('ajax_email_register');