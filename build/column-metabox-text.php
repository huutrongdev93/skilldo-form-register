$columnsNew['{{name}}'] = [
    'label'  => '{{label}}',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('{{name}}', $item, $args)->value(function($item, \SkillDo\Cms\Table\Columns\ColumnText $column) {
        $metadata = \FormRegister\Models\FormRegisterResult::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_{{formKey}}_data', $metadata, $column);
    })
];