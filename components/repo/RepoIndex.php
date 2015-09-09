<?php

class RepoIndex extends Form {

    public function repoDef() {
        ## repoDef() function should be overridden
        return [];
    }

    public function getRepoDef() {
        return @$this->repoDef();
    }

    public function getPattern() {
        return @$this->repoDef['pattern'];
    }

    public function getPath() {
        return @$this->repoDef['path'];
    }

    public function getForm() {
        return [
            'title' => 'Repo List Directory',
            'layout' => [
                'name' => 'full-width',
                'data' => [
                    'col1' => [
                        'type' => 'mainform',
                        'size' => '100',
                    ],
                ],
            ],
        ];
    }

    public function getFields() {
        return [
            [
                'linkBar' => [
                    [
                        'label' => 'Tambah {{ params.t }}',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => [
                            'href' => 'url:{tambahUrl}',
                        ],
                        'type' => 'LinkButton',
                    ],
                ],
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ],
            [
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => [
                ],
                'type' => 'DataFilter',
            ],
            [
                'name' => 'dataSource1',
                'fieldType' => 'php',
                'php' => 'RepoManager::listAll($model->path,$model->pattern, $params);',
                'params' => [
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    'where' => 'dataFilter1',
                ],
                'enablePaging' => 'Yes',
                'pagingPHP' => 'RepoManager::count($model->path);',
                'type' => 'DataSource',
            ],
            [
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => [
                    [
                        'name' => 'file',
                        'label' => 'file',
                        'options' => [
                            'visible' => 'false',
                        ],
                        'inputMask' => '',
                        'stringAlias' => [],
                        'columnType' => 'string',
                        'show' => false,
                    ],
                    [
                        'name' => 'id',
                        'label' => '',
                        'options' => [
                            'visible' => 'false',
                        ],
                        'inputMask' => '',
                        'stringAlias' => [],
                        'columnType' => 'string',
                        'show' => false,
                    ],
                ],
                'gridOptions' => [
                    'enablePaging' => 'true',
                    'generateColumns' => 'true',
                ],
                'type' => 'DataGrid',
            ],
        ];
    }

}
