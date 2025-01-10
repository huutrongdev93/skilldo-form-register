<?php
class Form_Register extends \FormRegister\Model\Form {}
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

        $forms = \FormRegister\Model\Form::gets();

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