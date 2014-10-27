<?php
class Repo extends Form {
	
    public function getForm() {
        return array (
            'title' => 'Repo',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'size' => '100',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<div>
',
                'type' => 'Text',
            ),
            array (
                'name' => 'dataSource1',
                'fieldType' => 'php',
                'php' => 'RepoManager::model()->browse()[\\\'item\\\'];',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'name',
                        'label' => 'name',
                        'options' => array (),
                        'typeOptions' => array (
                            'string' => array (
                                'inputMask',
                                'options',
                            ),
                            'buttons' => array (
                                'buttonCollapsed',
                                'buttons',
                                'options',
                            ),
                            'dropdown' => array (
                                'listType',
                                'listExpr',
                                'listMustChoose',
                                'options',
                            ),
                            'relation' => array (
                                'relParams',
                                'relCriteria',
                                'relModelClass',
                                'relIdField',
                                'relLabelField',
                                'options',
                            ),
                        ),
                        'inputMask' => '',
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                    ),
                    array (
                        'name' => 'type',
                        'label' => 'type',
                        'options' => array (),
                        'typeOptions' => array (
                            'string' => array (
                                'inputMask',
                                'options',
                            ),
                            'buttons' => array (
                                'buttonCollapsed',
                                'buttons',
                                'options',
                            ),
                            'dropdown' => array (
                                'listType',
                                'listExpr',
                                'listMustChoose',
                                'options',
                            ),
                            'relation' => array (
                                'relParams',
                                'relCriteria',
                                'relModelClass',
                                'relIdField',
                                'relLabelField',
                                'options',
                            ),
                        ),
                        'inputMask' => '',
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                    ),
                    array (
                        'name' => 'path',
                        'label' => 'path',
                        'options' => array (),
                        'typeOptions' => array (
                            'string' => array (
                                'inputMask',
                                'options',
                            ),
                            'buttons' => array (
                                'buttonCollapsed',
                                'buttons',
                                'options',
                            ),
                            'dropdown' => array (
                                'listType',
                                'listExpr',
                                'listMustChoose',
                                'options',
                            ),
                            'relation' => array (
                                'relParams',
                                'relCriteria',
                                'relModelClass',
                                'relIdField',
                                'relLabelField',
                                'options',
                            ),
                        ),
                        'inputMask' => '',
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => '',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => '',
                        ),
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                    ),
                ),
                'type' => 'DataGrid',
            ),
            array (
                'value' => '</div>
',
                'type' => 'Text',
            ),
        );
    }

}