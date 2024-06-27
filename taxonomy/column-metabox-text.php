$columnsNew['{{name}}'] = [
    'label'  => '{{label}}',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('{{name}}', $item, $args)->value(function($item, \SkillDo\Table\Columns\ColumnText $column) {
        $metadata = Form_Register_Result::getMeta($item->id, $column->getName(), true);
        return apply_filters('generate_admin_table_{{formKey}}_data', $metadata, $column);
    })
];