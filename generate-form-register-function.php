<?php
Class Form_Register extends \SkillDo\Model\Model {

    protected string $table = 'generate_form_register';

    protected array $columns = [
        'name'  => ['string'],
        'key'   => ['string'],
        'field' => ['array', []],
        'url_redirect'  => ['string'],
        'email_template' => ['wysiwyg'],
        'is_live'   => ['int', 1],
        'is_redirect' => ['int', 0],
        'send_email' => ['int', 0],
    ];

    protected array $rules = [
        'add'               => [
            'require' => [
                'key' => 'Form Key không được để trống'
            ]
        ],
    ];
}

class Form_Register_Result extends \SkillDo\Model\Model
{
    protected string $table = 'form_register_result';

    protected array $columns = [
        'name' => ['string'],
        'email' => ['string'],
        'phone' => ['string'],
        'message' => ['string'],
        'status' => ['int', 0],
        'form_key' => ['string'],
    ];

    protected array $rules = [
        'add'               => [
            'require' => [
                'form_key' => 'Form Key không được để trống'
            ]
        ],
    ];
}

Class Form_Register_Helper {
    static function config($field = '', $type = ''): array
    {

        if(empty($field)) return [];

        $config = [];

        $field = explode("\n", $field);

        foreach ($field as $key => $value) {

            $value = explode('|', $value);

            if(count($value) >= 5) {

                if($type == 'data' && $value[3] != 'data') continue;

                if($type == 'metadata' && $value[3] != 'metadata') continue;

                $config[$value[0]] = [
                    'field' => $value[1],
                    'label' => $value[2],
                    'type'  => $value[3],
                    'table_show' => $value[4],
                ];

                if(isset($value[5])) {
                    $config[$value[0]]['rule'] = explode(',', $value[5]);
                }
            }
        }

        return $config;
    }
    static function generateCode($form = []): false|string
    {
        $formKey = $form->key;

        $fields = unserialize($form->field);

        $storage = Storage::make('views/plugins/generate-form-register/taxonomy');

		$taxonomyString = $storage->get('taxonomy.php');

        $taxonomyString = str_replace('{{formKey}}', $formKey, $taxonomyString);

        $taxonomyString = str_replace('{{name}}', $form->name, $taxonomyString);

        $columnsNew = '';

        foreach ($fields['default'] as $column => $input) {

            if(empty($input['use'])) continue;

            $columnCode = $storage->get('column-text.php');

            $columnCode = str_replace('{{name}}', $column, $columnCode);

            $columnCode = str_replace('{{label}}', $input['label'], $columnCode);

            $columnsNew .= $columnCode."\n";
        }

        foreach ($fields['metadata'] as $column => $input) {

            $columnCode = $storage->get('column-metabox-text.php');

            $columnCode = str_replace('{{formKey}}', $formKey, $columnCode);

            $columnCode = str_replace('{{name}}', $input['name'], $columnCode);

            $columnCode = str_replace('{{label}}', $input['label'], $columnCode);

            $columnsNew .= $columnCode."\n";
        }

        return str_replace('{{columnsNew}}', $columnsNew, $taxonomyString);
    }
    static function build(): void
    {
        $storage = Storage::make('views/plugins/generate-form-register/taxonomy');

        if($storage->fileExists('taxonomy.build.php')) {
            $storage->delete('taxonomy.build.php');
        }

        $forms = Form_Register::gets();

        $codeMain = '<?php'."\n";

        foreach ($forms as $key => $form) {

            if($form->is_live == 1) {
                $code = '';
                $code = static::generateCode($form);
                $code .= "\n";
                $codeMain .= $code;
            }
        }

        $storage->put('taxonomy.build.php', $codeMain);
    }
}