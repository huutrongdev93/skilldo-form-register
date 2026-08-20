$columnsNew['{{name}}'] = [
    'label'  => '{{label}}',
    'column' => fn($item, $args) => \SkillDo\Cms\Table\Columns\ColumnText::make('{{name}}', $item, $args)
];