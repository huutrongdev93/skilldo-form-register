<?php
namespace FormRegister\Ajax\Admin;

use FormRegister\Supports\FormRegisterHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SkillDo\Cms\Plugin\Plugin;
use SkillDo\Cms\Support\Url;
use SkillDo\Http\Request;
use Illuminate\Support\Str;
use SkillDo\Support\Path;
use SkillDo\Validate\Rule;

class FormRegisterAjax
{
    static function adminSave(Request $request): void
    {
        $validate = $request->validate([
            'key' => Rule::make('Key form')->notEmpty(),
            'fieldName' => Rule::make('Cấu hình cho trường name')->notEmpty(),
            'fieldEmail' => Rule::make('Cấu hình cho trường email')->notEmpty(),
            'fieldPhone' => Rule::make('Cấu hình cho trường phone')->notEmpty(),
            'fieldMessage' => Rule::make('Cấu hình cho trường message')->notEmpty(),
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $key = $request->input('key');

        $id  = $request->input('id');

        $data = $request->input();

        unset($data['action']);
        unset($data['post_type']);
        unset($data['cate_type']);
        unset($data['csrf_test_name']);

        $field = [
            'default' => [],
            'metadata' => []
        ];

        $field['default']['name'] = $data['fieldName'];

        $field['default']['email'] = $data['fieldEmail'];

        $field['default']['phone'] = $data['fieldPhone'];

        $field['default']['message'] = $data['fieldMessage'];

        unset($data['fieldName']);

        unset($data['fieldEmail']);

        unset($data['fieldPhone']);

        unset($data['fieldMessage']);

        if(!empty($data['metaData']))
        {
            $field['metadata'] = $data['metaData'];
            unset($data['metaData']);
        }

        $data['field'] = $field;

        //Thêm mới
        if(empty($id))
        {
            if(\FormRegister\Models\FormRegister::where('key', $key)->count() != 0)
            {
                response()->error('Key form đã tồn tại');
            }

            if(!isset($data['key'])) $data['key'] = $key;

            if(!isset($data['is_live'])) $data['is_live'] = 1;

            $error = \FormRegister\Models\FormRegister::create($data);

            if(is_skd_error($error))
            {
                response()->error($error);
            }

            FormRegisterHelper::build();

            response()->success(trans('ajax.add.success'));
        }
        //Cập nhật
        else 
        {
            $form   = \FormRegister\Models\FormRegister::find($id);

            if(!hasItems($form))
            {
                response()->error(trans('Form không tồn tại'));
            }
            
            foreach ($data as $key => $value)
            {
                if($key  == 'email_template')
                {
                    $form->{$key} = $value;
                }
                else
                {
                    $form->{$key} = (!hasItems($value)) ? trim(Str::clear($value)) : $value;
                }
            }

            if(!$request->has('is_live')) $form->is_live = 0;

            if(!$request->has('send_email')) $form->send_email = 0;

            if(!$request->has('send_telegram') && Plugin::isActive('telegram')) $form->send_telegram = 0;

            if(!$request->has('is_redirect')) $form->is_redirect = 0;

            $form->save();

            FormRegisterHelper::build();

            response()->success(trans('ajax.update.success'));
        }

        response()->error(trans('ajax.update.error'));
    }

    static function quickCreate(Request $request): void
    {
        $validate = $request->validate([
            'key' => Rule::make('form key')->notEmpty(),
            'formId' => Rule::make('form id')->notEmpty()
        ]);

        if ($validate->fails())
        {
            response()->error($validate->errors());
        }

        $key = $request->input('key');

        $formId = $request->input('formId');

        $data = $request->input($formId);

        $field = [
            'default' => [
                'name' => $data['fieldName'],
                'email' => $data['fieldEmail'],
                'phone' => $data['fieldPhone'],
                'message' => $data['fieldMessage'],
            ],
            'metadata' => [
            ]
        ];

        unset($data['fieldName']);

        unset($data['fieldEmail']);

        unset($data['fieldPhone']);

        unset($data['fieldMessage']);

        if(!empty($data['metaData']))
        {
            $field['metadata'] = $data['metaData'];
            unset($data['metaData']);
        }

        $data['field'] = $field;

        if(\FormRegister\Models\FormRegister::where('key', $key)->count() != 0)
        {
            response()->error('Key form đã tồn tại');
        }

        if(!isset($data['key'])) $data['key'] = $key;

        if(!isset($data['is_live'])) $data['is_live'] = 1;

        $error = \FormRegister\Models\FormRegister::insert($data);

        if(is_skd_error($error))
        {
            response()->error($error);
        }

        FormRegisterHelper::build();

        response()->success(trans('ajax.add.success'));
    }

    static function export(Request $request): void
    {
        $exportType = $request->input('exportType');

        $search = $request->input('search');

        if(empty($search['form-key']))
        {
            response()->error(trans('Không xác định được loại form cần xuất'));
        }

        $formKey = trim($search['form-key']);

        $args  = \FormRegister\Models\FormRegisterResult::where('form_key', $formKey);

        if($exportType === 'pageCurrent') {

            $listId = $request->input('listData');

            if(!hasItems($listId))
            {
                response()->error(trans('Không có dữ liệu nào để xuất'));
            }

            $args->whereIn('id', $listId);
        }

        if($exportType === 'check') {

            $listId = $request->input('listData');

            if(!hasItems($listId)) {
                response()->error(trans('Không có dữ liệu nào để xuất'));
            }

            $args->whereIn('id', $listId);
        }

        if($exportType === 'searchCurrent') {

            if(!empty($search['time'])) {
                $time = explode(' - ', $search['time']);
                if(hasItems($time) && count($time) == 2) {
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

        $formResults = $args->get();

        $form = \FormRegister\Models\FormRegister::where('key', $formKey)->first();

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
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
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
                    return \FormRegister\Models\FormRegisterResult::getMeta($item->id, $input['name'], true);
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
            'right' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'left'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ];

        $alignment['vertical'] = [
            'top'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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

        $filePathData = Path::storage('cms/export/');

        if(!file_exists($filePathData))
        {
            mkdir($filePathData, 0755);
            chmod($filePathData, 0755);
        }

        $filename = 'form-'.md5(time()).'-'.date('d-m-Y').'.xlsx';

        $writer->save($filePathData.$filename);

        $path = Url::base().$filePathData.$filename;

        response()->success(trans('ajax.load.success'), $path);
    }
}