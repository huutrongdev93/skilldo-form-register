$columnsNew['{{name}}'] = [
    'label'  => '{{label}}',
    'column' => fn($item, $args) => \SkillDo\Table\Columns\ColumnText::make('{{name}}', $item, $args)
];