<?php

class GenericGenerator extends FormGenerator {

    public function steps() {
        return [
            's1' => [
                'title' => 'Create Model File',
                'function' => 'generateModel',
                'output' => [
                    [
                        'path' => 'app.models',
                        'name' => '{$modelClass}.php'
                    ]
                ]
            ],
            's2' => [
                'title' => 'Generating Form',
                'function' => 'generateForm',
                'output' => [
                    [
                        'path' => 'app.modules.{$module}.forms.{$formDir}',
                        'name' => '{$fileIndex}.php'
                    ],
                    [
                        'path' => 'app.modules.{$module}.forms.{$formDir}',
                        'name' => '{$fileForm}.php'
                    ]
                ]
            ],
            's3' => [
                'title' => 'Generating Form',
                'function' => 'generateForm',
                'output' => [
                    [
                        'path' => 'app.modules.{$module}.forms.{$formDir}',
                        'name' => '{$fileIndex}.php'
                    ],
                    [
                        'path' => 'app.modules.{$module}.forms.{$formDir}',
                        'name' => '{$fileForm}.php'
                    ]
                ]
            ],
        ];
    }

}
