<?php
if(!Admin::is()) return;

function GenerateFormRegisterUpdateCore(): void
{
    if(Admin::is() && Auth::check()) {

        $version = Option::get('generate_form_register_version');

        $version = (empty($version)) ? '3.2.2' : $version;

        if (version_compare(G_FORM_REGISTER_VERSION, $version) === 1) {
            $update = new GenerateFormRegisterUpdateVersion();
            $update->runUpdate($version);
        }
    }
}
add_action('admin_init', 'GenerateFormRegisterUpdateCore');

Class GenerateFormRegisterUpdateVersion {
    public function runUpdate($versionCurrent): void
    {
        $listVersion    = ['4.0.0'];

        foreach ($listVersion as $version ) {
            if(version_compare( $version, $versionCurrent ) == 1) {
                $function = 'update_Version_'.str_replace('.','_',$version);
                if(method_exists($this, $function)) $this->$function();
            }
        }

        Option::update('generate_form_register_version', G_FORM_REGISTER_VERSION );
    }
    public function update_Version_4_0_0(): void
    {
        GenerateFormRegisterUpdateDatabase::Version_4_0_0();
        GenerateFormRegisterUpdateFiles::Version_4_0_0();
    }
}

Class GenerateFormRegisterUpdateDatabase {
    public static function Version_4_0_0(): void
    {
        (include 'database/db_v4.0.0.php')->up();

        $forms = Form_Register::gets();

        foreach ($forms as $form) {

            $taxonomyConfig = explode("\n", $form->field);

            $fieldDefault = [];

            $fieldMeta = [];

            $fieldMetaSpecial = [];

            foreach ($taxonomyConfig as $field) {

                $field = explode("\n", $field);

                foreach ($field as $key => $value) {

                    $value = explode('|', $value);

                    if(count($value) >= 5) {

                        if($value[3] == 'data') {

                            if(in_array($value[0], ['name', 'fullname', 'email', 'phone', 'note', 'message'])) {
                                $fieldDefault[$value[0]] = [
                                    'field' => $value[1],
                                    'label' => $value[2],
                                    'rule'  => (!empty($value[5])) ? explode(',', $value[5]) : []
                                ];
                            }
                            else {
                                $fieldMetaSpecial[$value[1]] = [
                                    'field' => $value[0],
                                    'label' => $value[2],
                                    'rule'  => (!empty($value[5])) ? explode(',', $value[5]) : []
                                ];
                            }
                        }

                        if($value[3] == 'metadata') {
                            $fieldMeta[$value[0]] = [
                                'field' => $value[1],
                                'label' => $value[2],
                                'rule'  => (!empty($value[5])) ? explode(',', $value[5]) : []
                            ];
                        }
                    }
                }
            }

            $posts = Posts::where('post_type', $form->key)
                ->where('public', '<>', null)
                ->where('trash', '<>', null)
                ->fetch();

            $form_register_result = [];

            foreach ($posts as $post) {

                $result = [];

                if(isset($fieldDefault['name']) && isset($post->{$fieldDefault['name']['field']})) {
                    $result['name'] = $post->{$fieldDefault['name']['field']};
                }
                if(isset($fieldDefault['fullname']) && isset($post->{$fieldDefault['fullname']['field']})) {
                    $result['name'] = $post->{$fieldDefault['fullname']['field']};
                }
                if(isset($fieldDefault['email']) && isset($post->{$fieldDefault['email']['field']})) {
                    $result['email'] = $post->{$fieldDefault['email']['field']};
                }
                if(isset($fieldDefault['phone']) && isset($post->{$fieldDefault['email']['field']})) {
                    $result['phone'] = $post->{$fieldDefault['phone']['field']};
                }
                if(isset($fieldDefault['note']) && isset($post->{$fieldDefault['note']['field']})) {
                    $result['message'] = $post->{$fieldDefault['note']['field']};
                }
                if(isset($fieldDefault['message']) && isset($post->{$fieldDefault['message']['field']})) {
                    $result['message'] = $post->{$fieldDefault['message']['field']};
                }

                if(!empty($result)) {

                    $result['post_id'] = $post->id;

                    $result['form_key'] = $form->key;

                    $result['created'] = $post->created;

                    $form_register_result[] = $result;
                }

                foreach($post as $column => $value) {
                    if(!empty($fieldMetaSpecial[$column])) {
                        $fieldMetaSpecial[$column]['value'] = $value;
                    }
                }
            }

            $field = [
                'default' => [
                    'name' => [
                        'use'       => 0,
                        'field'     => 'name',
                        'label'     => '',
                        'required'  => 1,
                        'limit'     => 0,
                    ],
                    'email' => [
                        'use'       => 0,
                        'field'     => 'email',
                        'label'     => '',
                        'required'  => 1,
                        'isEmail'   => 1,
                    ],
                    'phone' => [
                        'use'       => 0,
                        'field'     => 'phone',
                        'label'     => '',
                        'required'  => 1,
                        'isPhone'   => 0,
                    ],
                    'message' => [
                        'use'       => 1,
                        'field'     => 'note',
                        'label'     => '',
                        'required'  => 1,
                    ]
                ],
                'metadata' => [],
            ];

            if(have_posts($fieldDefault)) {
                if(isset($fieldDefault['name'])) {
                    $field['default']['name'] = [
                        'use'       => 1,
                        'field'     => 'name',
                        'label'     => $fieldDefault['name']['label'],
                        'required'  => 1,
                        'limit'     => 0,
                    ];
                }
                if(isset($fieldDefault['fullname'])) {
                    $field['default']['name'] = [
                        'use'       => 1,
                        'field'     => 'fullname',
                        'label'     => $fieldDefault['fullname']['label'],
                        'required'  => 1,
                        'limit'     => 0,
                    ];
                }
                if(isset($fieldDefault['email'])) {
                    $field['default']['email'] = [
                        'use'       => 1,
                        'field'     => 'email',
                        'label'     => $fieldDefault['email']['label'],
                        'required'  => 1,
                        'isEmail'   => 1,
                    ];
                }
                if(isset($fieldDefault['phone'])) {
                    $field['default']['phone'] = [
                        'use'       => 1,
                        'field'     => 'phone',
                        'label'     => $fieldDefault['phone']['label'],
                        'required'  => 1,
                        'isPhone'   => 0,
                    ];
                }
                if(isset($fieldDefault['note'])) {
                    $field['default']['message'] = [
                        'use'       => 1,
                        'field'     => 'note',
                        'label'     => $fieldDefault['note']['label'],
                        'required'  => 1,
                    ];
                }
                if(isset($fieldDefault['message'])) {
                    $field['default']['message'] = [
                        'use'       => 1,
                        'field'     => 'message',
                        'label'     => $fieldDefault['message']['label'],
                        'required'  => 1,
                    ];
                }
            }

            if(have_posts($fieldMeta)) {
                foreach ($fieldMeta as $fieldName => $data) {
                    $field['metadata'][$fieldName] = [
                        'use'       => 1,
                        'name'      => $fieldName,
                        'field'     => $fieldName,
                        'label'     => $data['label'],
                        'required'  => (!empty($data['rule']) && in_array('required', $data['rule'])) ? 1 : 0,
                    ];
                }
            }

            foreach ($form_register_result as $post) {

                $post_id = $post['post_id'];

                unset($post['post_id']);

                $id = model('form_register_result')->add($post);

                if(!empty($id)) {

                    foreach ($field['metadata'] as $fieldName => $meta) {
                        Form_Register_Result::updateMeta($id, $fieldName, Posts::getMeta($post_id, $fieldName, true));
                    }

                    foreach ($fieldMetaSpecial as $fieldName => $meta) {
                        Form_Register_Result::updateMeta($id, $meta['field'], $meta['value']);
                    }
                }
            }

            if(have_posts($fieldMetaSpecial)) {
                foreach ($fieldMetaSpecial as $fieldName => $data) {
                    $field['metadata'][$data['field']] = [
                        'use'       => 1,
                        'name'      => $data['field'],
                        'field'     => $data['field'],
                        'label'     => $data['label'],
                        'required'  => (!empty($data['rule']) && in_array('required', $data['rule'])) ? 1 : 0,
                    ];
                }
            }

            Form_Register::where('id', $form->id)->update([
                'field' => serialize($field)
            ]);

            Form_Register_Helper::build();
        }
    }
}

Class GenerateFormRegisterUpdateFiles {

    public static function Version_4_0_0(): void
    {
        $storage = Storage::disk('plugin');
        $storage->deleteDirectory('generate-form-register/admin/html');
        $storage->deleteDirectory('generate-form-register/email-template');
    }
}