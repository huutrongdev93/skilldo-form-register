<?php
namespace FormRegister\Supports;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormRegisterHelper
{
    static function config($field = '', $type = ''): array
    {
        if(empty($field)) return [];

        $config = [];

        $field = explode("\n", $field);

        foreach ($field as $value)
        {
            $value = explode('|', $value);

            if(count($value) >= 5)
            {
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

        $storage = Storage::disk('plugins');

        $taxonomyString = $storage->get('generate-form-register/build/build.php');

        $taxonomyString = Str::of($taxonomyString)
                                ->replace('FORM_KEY_CLASS_NAME', Str::studly($form->key))
                                ->replace('FORM_KEY', str_replace('-', '_', $form->key))
                                ->replace('FORM_NAME', $form->name);

        $columnsNew = '';

        foreach ($fields['default'] as $column => $input)
        {
            if(empty($input['use'])) continue;

            $columnCode = $storage->get('generate-form-register/build/column-text.php');

            $columnCode = str_replace('{{name}}', $column, $columnCode);

            $columnCode = str_replace('{{label}}', $input['label'], $columnCode);

            $columnsNew .= $columnCode."\n";
        }

        foreach ($fields['metadata'] as $input)
        {
            $columnCode = $storage->get('generate-form-register/build/column-metabox-text.php');

            $columnCode = str_replace('{{formKey}}', $formKey, $columnCode);

            $columnCode = str_replace('{{name}}', $input['name'], $columnCode);

            $columnCode = str_replace('{{label}}', $input['label'], $columnCode);

            $columnsNew .= $columnCode."\n";
        }

        return str_replace('COLUMNS_NEW', $columnsNew, $taxonomyString);
    }

    static function build(): void
    {
        $storage = Storage::disk('plugins');

        $storage->delete($storage->files('generate-form-register/app/Builds'));

        $storage->delete('generate-form-register/bootstrap/build.php');

        $forms = \FormRegister\Models\FormRegister::all();

        $bootstraps = '';

        foreach ($forms as $form)
        {
            if($form->is_live == 1)
            {
                $className = Str::studly($form->key);

                $bootstraps .= Str::of($storage->get('generate-form-register/build/hooks.php'))
                    ->replace('FORM_KEY_CLASS_NAME', $className)
                    ->replace('FORM_KEY', str_replace('-', '_', $form->key))
                    ->toString()."\n";

                $storage->put('generate-form-register/app/Builds/'.$className.'Build.php', static::generateCode($form));
            }
        }

        if(!empty($bootstraps))
        {
            $bootstraps = '<?php'."\n".$bootstraps;

            $storage->put('generate-form-register/bootstrap/build.php', $bootstraps);
        }

        cmsClearCacheAutoLoad();
    }
}