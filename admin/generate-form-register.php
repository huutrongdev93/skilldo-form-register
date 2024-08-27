<?php
class GenerateFormRegisterAdmin {
    static function register($tabs) {
        if(Auth::hasCap('generate_form_register')) {
            $tabs['generate_form_register'] = [
				'group'         => 'marketing',
                'label'         => 'Form đăng ký',
                'description'   => 'Quản lý form đăng ký, booking',
                'callback'      => 'GenerateFormRegisterAdmin::render',
                'icon'          => '<i class="fad fa-mailbox"></i>',
	            'form' => false
            ];
        }
        return $tabs;
    }
    static function render(\SkillDo\Http\Request $request): void {

        $view = $request->input('view');

        if(empty($view)) $view = 'list';

        Plugin::view('generate-form-register', 'views/admin/form-tabs', ['view' => $view]);

        $data = [];

        if($view == 'list') {
            $data['forms'] = Form_Register::gets();
            Plugin::view('generate-form-register', 'views/admin/form-list', $data);
        }

        if($view == 'add' || $view == 'edit') {
            GenerateFormRegisterAdmin::pageSave($request);
        }

        if($view == 'sample') {

            $dataFormSample = [
                (object)[
                    'key'               => 'email_register',
                    'name'              => 'Đăng ký nhận tin',
                    'is_live'           => 1,
	                'send_email'        => 0,
                    'field'             => [
						'default' => [
                            'name' => [
                                'use'       => 0,
                                'field'     => 'name',
                                'label'     => '',
                                'required'  => 1,
                                'limit'     => 0,
                            ],
                            'email' => [
                                'use'       => 1,
                                'field'     => 'email',
                                'label'     => 'Email',
                                'required'  => 1,
                                'isEmail'   => 0,
                            ],
                            'phone' => [
                                'use'       => 0,
                                'field'     => 'phone',
                                'label'     => 'Số điện thoại',
                                'required'  => 1,
                                'isPhone'   => 0,
                            ],
                            'message' => [
                                'use'       => 0,
                                'field'     => 'note',
                                'label'     => 'Ghi chú',
                                'required'  => 1,
                            ]
                        ],
                        'metadata' => [],
                    ],
                    'sample'            => '<p>email</p>'
                ],
                (object)[
                    'key'               => 'register_contact',
                    'name'              => 'Đăng ký tư vấn',
                    'is_live'           => 1,
	                'send_email'  => 0,
                    'field'             => [
                        'default' => [
                            'name' => [
                                'use'       => 1,
                                'field'     => 'name',
                                'label'     => 'Họ và tên',
                                'required'  => 1,
                                'limit'     => 0,
                            ],
                            'email' => [
                                'use'       => 1,
                                'field'     => 'email',
                                'label'     => 'Email',
                                'required'  => 1,
                                'isEmail'   => 0,
                            ],
                            'phone' => [
                                'use'       => 1,
                                'field'     => 'phone',
                                'label'     => 'Số điện thoại',
                                'required'  => 1,
                                'isPhone'   => 0,
                            ],
                            'message' => [
                                'use'       => 1,
                                'field'     => 'note',
                                'label'     => 'Ghi chú',
                                'required'  => 1,
                            ]
                        ],
                        'metadata' => [],
                    ],
                    'sample'            => '<p>name</p> <p>email</p> <p>phone</p> <p>note</p>'
                ],
                (object)[
                    'key'               => 'register_booking',
                    'name'              => 'BOOKING',
                    'is_live'           => 1,
	                'send_email'        => 0,
                    'field'             => [
                        'default' => [
                            'name' => [
                                'use'       => 1,
                                'field'     => 'name',
                                'label'     => 'Họ và tên',
                                'required'  => 1,
                                'limit'     => 0,
                            ],
                            'email' => [
                                'use'       => 1,
                                'field'     => 'email',
                                'label'     => 'Email',
                                'required'  => 1,
                                'isEmail'   => 0,
                            ],
                            'phone' => [
                                'use'       => 1,
                                'field'     => 'phone',
                                'label'     => 'Số điện thoại',
                                'required'  => 1,
                                'isPhone'   => 0,
                            ],
                            'message' => [
                                'use'       => 0,
                                'field'     => 'note',
                                'label'     => 'Ghi chú',
                                'required'  => 1,
                            ]
                        ],
                        'metadata' => [
                            uniqid() => [
                                'use'       => 1,
                                'name'     => 'time',
                                'field'     => 'time',
                                'label'     => 'Giờ',
                                'required'  => 1,
                            ],
                            uniqid() => [
                                'use'       => 1,
                                'name'     => 'data',
                                'field'     => 'data',
                                'label'     => 'Ngày',
                                'required'  => 1,
                            ],
                        ],
                    ],
                    'sample'            => ' <p>name</p> <p>email</p> <p>phone</p> <p>time</p> <p>date</p>'
                ],
            ];

			$forms = [];

			foreach ($dataFormSample as $key => $form) {

                [$formDefault, $formMeta] = static::form($form->field['default'], $form->field['metadata'], $form->key);

                $forms[$key] = [
					'default'  => $formDefault,
					'metadata' => $formMeta,
                ];
			}

            Plugin::view('generate-form-register', '/views/admin/form-sample', [
				'dataFormSample' => $dataFormSample,
				'forms' => $forms
            ]);
        }
    }
	static function pageSave(\SkillDo\Http\Request $request): void
    {
		$id = $request->input('id');

        $form = null;

		if(!empty($id)) {

            $form = Form_Register::get($id);

			if(!have_posts($form)) {
				echo Admin::alert('error', 'Không tìm thấy form có id là '. $id);
				return;
			}

            $form->field = unserialize($form->field);

			$fieldDefault = $form->field['default'] ?? null;

			$fieldMetadata = $form->field['metadata'] ?? null;
		}

		[$formDefault, $formMeta] = static::form(
            (isset($fieldDefault)) ? $fieldDefault : null,
            (isset($fieldMetadata)) ? $fieldMetadata : null,
		);

        Plugin::view('generate-form-register', 'views/admin/form-save', [
			'formDefault' => $formDefault,
			'formMeta' => $formMeta,
			'form' => $form
        ]);
	}
	static function form($fieldDefault = null, $fieldMetadata = null, $key = null): array
    {
        $formDefault = form();
        $formName = form();
        $name = (!empty($key)) ? $key.'[fieldName]' : 'fieldName';
        $formName->switch($name.'[use]', [
            'label' => 'Sử dụng',
            'start' => 2
        ], (isset($fieldDefault['name']['use'])) ? $fieldDefault['name']['use'] : 1);
        $formName->text($name.'[field]', [
            'label' => 'Tên Field data gửi lên',
            'start' => 3
        ], (isset($fieldDefault['name']['field'])) ? $fieldDefault['name']['field'] : 'name');
        $formName->text($name.'[label]', [
            'label' => 'Tiêu đề Field',
            'start' => 3
        ], (isset($fieldDefault['name']['label'])) ? $fieldDefault['name']['label'] : 'Họ và tên');
        $formName->switch($name.'[required]', [
            'label' => 'Không cho phép bỏ trống',
            'start' => 2
        ], (isset($fieldDefault['name']['required'])) ? $fieldDefault['name']['required'] : 1);
        $formName->number($name.'[limit]', [
            'label' => 'Số ký tự nhập tối thiểu',
            'start' => 2
        ], (isset($fieldDefault['name']['limit'])) ? $fieldDefault['name']['limit'] : 0);
        $formDefault->addGroup($formName, [
            'start' => '<div class="store_wg_item row m-1"><h5 class="mt-3 mb-2">Trường họ tên</h5>',
            'end' => '</div>',
        ]);

        $formEmail = form();
        $name = (!empty($key)) ? $key.'[fieldEmail]' : 'fieldEmail';
        $formEmail->switch($name.'[use]', [
            'label' => 'Sử dụng',
            'start' => 2
        ], (isset($fieldDefault['email']['use'])) ? $fieldDefault['email']['use'] : 1);
        $formEmail->text($name.'[field]', [
            'label' => 'Tên Field data gửi lên',
            'start' => 3
        ], (isset($fieldDefault['email']['field'])) ? $fieldDefault['email']['field'] : 'email');
        $formEmail->text($name.'[label]', [
            'label' => 'Tiêu đề Field',
            'start' => 3
        ], (isset($fieldDefault['email']['label'])) ? $fieldDefault['email']['label'] : 'Email');
        $formEmail->switch($name.'[required]', [
            'label' => 'Không cho phép bỏ trống',
            'start' => 2
        ], (isset($fieldDefault['email']['required'])) ? $fieldDefault['email']['required'] : 1);
        $formEmail->switch($name.'[isEmail]', [
            'label' => 'Kiểm tra cấu trúc email',
            'start' => 2
        ], (isset($fieldDefault['email']['isEmail'])) ? $fieldDefault['email']['isEmail'] : 0);
        $formDefault->addGroup($formEmail, [
            'start' => '<div class="store_wg_item row m-1"><h5 class="mt-3 mb-2">Trường email</h5>',
            'end' => '</div>',
        ]);

        $formPhone = form();
        $name = (!empty($key)) ? $key.'[fieldPhone]' : 'fieldPhone';
        $formPhone->switch($name.'[use]', [
            'label' => 'Sử dụng',
            'start' => 2
        ], (isset($fieldDefault['phone']['use'])) ? $fieldDefault['phone']['use'] : 1);
        $formPhone->text($name.'[field]', [
            'label' => 'Tên Field data gửi lên',
            'start' => 3
        ], (isset($fieldDefault['phone']['field'])) ? $fieldDefault['phone']['field'] : 'phone');
        $formPhone->text($name.'[label]', [
            'label' => 'Tiêu đề Field',
            'start' => 3
        ], (isset($fieldDefault['phone']['label'])) ? $fieldDefault['phone']['label'] : 'Số điện thoại');
        $formPhone->switch($name.'[required]', [
            'label' => 'Không cho phép bỏ trống',
            'start' => 2
        ], (isset($fieldDefault['phone']['required'])) ? $fieldDefault['phone']['required'] : 1);
        $formPhone->switch($name.'[isPhone]', [
            'label' => 'Kiểm tra cấu trúc SĐT',
            'start' => 2
        ], (isset($fieldDefault['phone']['isPhone'])) ? $fieldDefault['phone']['isPhone'] : 0);
        $formDefault->addGroup($formPhone, [
            'start' => '<div class="store_wg_item row m-1"><h5 class="mt-3 mb-2">Trường số điện thoại</h5>',
            'end' => '</div>',
        ]);

        $formNote = form();
        $name = (!empty($key)) ? $key.'[fieldMessage]' : 'fieldMessage';
        $formNote->switch($name.'[use]', [
            'label' => 'Sử dụng',
            'start' => 2
        ], (isset($fieldDefault['message']['use'])) ? $fieldDefault['message']['use'] : 1);
        $formNote->text($name.'[field]', [
            'label' => 'Tên Field data gửi lên',
            'start' => 3
        ], (isset($fieldDefault['message']['field'])) ? $fieldDefault['message']['field'] : 'note');
        $formNote->text($name.'[label]', [
            'label' => 'Tiêu đề Field',
            'start' => 3
        ], (isset($fieldDefault['message']['label'])) ? $fieldDefault['message']['label'] : 'Ghi chú');
        $formNote->switch($name.'[required]', [
            'label' => 'Không cho phép bỏ trống',
            'start' => 4
        ], (isset($fieldDefault['message']['required'])) ? $fieldDefault['message']['required'] : 0);
        $formDefault->addGroup($formNote, [
            'start' => '<div class="store_wg_item row m-1"><h5 class="mt-3 mb-2">Trường ghi chú</h5>',
            'end' => '</div>',
        ]);

        $formMeta = form();
        $name = (!empty($key)) ? $key.'[metaData]' : 'metaData';
        $formMeta->repeater($name, ['label' => 'Các trường thêm', 'fields' => [
            ['name' => 'name',  'type' => 'text',  'label' => 'Tiên biến', 'start' => 3],
            ['name' => 'field', 'type' => 'text',  'label' => 'Tên Field data gửi lên', 'start' => 3],
            ['name' => 'label', 'type' => 'text',  'label' => 'Tiêu đề hiển thị', 'language' => true, 'start' => 3],
            ['name' => 'required', 'type' => 'switch',  'label' => 'Không cho phép bỏ trống', 'start' => 3],
        ]], !empty($fieldMetadata) ? $fieldMetadata : []);

		return [$formDefault, $formMeta];
    }
    static function breadcrumb($breadcrumb, $pageIndex, \SkillDo\Http\Request $request): array
    {
        if($pageIndex == 'home_system') {

            $page = Url::segment(3);

            if($page == 'generate_form_register') {

                $view = request()->input('view');

                $view = (empty($view)) ? 'index' : $view;

                $breadcrumb['system_detail']['url'] = Url::admin('system/generate_form_register');

                if( $view == 'edit') {
                    $breadcrumb['system_detail']['active'] = false;
                    $breadcrumb['generate_form_register_edit'] = [
                        'active' => true,
                        'label' => trans('Chỉnh sữa form')
                    ];
                }
                if( $view == 'add') {
                    $breadcrumb['system_detail']['active'] = false;
                    $breadcrumb['generate_form_register_dd'] = [
                        'active' => true,
                        'label' => trans('Thêm form đăng ký')
                    ];
                }
            }
        }

        return $breadcrumb;
    }
}
add_filter('admin_breadcrumb', 'GenerateFormRegisterAdmin::breadcrumb', 50, 3);
add_filter('skd_system_tab', 'GenerateFormRegisterAdmin::register', 50);