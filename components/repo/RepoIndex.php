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
        return array(
            'title' => 'Repo List Directory',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array(
            array(
                'linkBar' => array(
                    array(
                        'label' => 'Tambah {{ params.t }}',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array(
                            'href' => 'url:{tambahUrl}',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array(
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array(
                ),
                'type' => 'DataFilter',
            ),
            array(
                'name' => 'dataSource1',
                'fieldType' => 'php',
                'php' => 'RepoManager::listAll($model->path,$model->pattern, $params);',
                'params' => array(
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    'where' => 'dataFilter1',
                ),
                'enablePaging' => 'Yes',
                'pagingPHP' => 'RepoManager::count($model->path);',
                'type' => 'DataSource',
            ),
            array(
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array(
                    array(
                        'name' => 'file',
                        'label' => 'file',
                        'options' => array(
                            'visible' => 'false',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array(),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array(
                        'name' => 'id',
                        'label' => '',
                        'options' => array(
                            'visible' => 'false',
                        ),
                        'inputMask' => '',
                        'stringAlias' => array(),
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array(
                    'enablePaging' => 'true',
                    'generateColumns' => 'true',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

}
