<?php

Yii::import("application.modules.nfy.models.NfyDbMessage");
class NfyMessagesIndex extends NfyDbMessage {
    
    public function getForm() {
        return array (
            'title' => 'Notification History',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'NfyMessagesIndex.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Mark all as read',
                        'icon' => 'check',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'href' => 'url:/widget/NfyWidget.markRead',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '{{ form.title }}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'filterType' => 'date',
                        'name' => 'created_on',
                        'label' => 'Date / Time',
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'show' => false,
                        'defaultValueFrom' => '',
                        'defaultValueTo' => '',
                    ),
                    array (
                        'filterType' => 'string',
                        'name' => 'message_id',
                        'label' => 'Message',
                        'defaultOperator' => '',
                        'defaultValue' => '',
                        'show' => false,
                    ),
                    array (
                        'filterType' => 'list',
                        'name' => 'status',
                        'label' => 'Status',
                        'defaultValue' => '',
                        'show' => false,
                        'listExpr' => '[\\\'1\\\'=>\\\'NEW\\\',\\\'2\\\'=>\\\'READ\\\']',
                    ),
                    array (
                        'name' => 'sender_id',
                        'label' => 'Sender',
                        'filterType' => 'relation',
                        'isCustom' => 'No',
                        'options' => array (),
                        'resetable' => 'Yes',
                        'defaultValue' => '',
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => 't.id, fullname, r.role_name',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => 'inner join p_user_role p on p.user_id = t.id
inner join p_role r on p.role_id = r.id',
                        ),
                        'relModelClass' => 'application.models.User',
                        'relIdField' => 'id',
                        'relLabelField' => '{fullname} ({role_name})',
                        'show' => false,
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'params' => array (
                    'where' => 'dataFilter1',
                    'paging' => 'dataGrid1',
                    'order' => 'dataGrid1',
                    ':sub_id' => '@$_GET[\\\'sub_id\\\']',
                ),
                'relationTo' => 'currentModel',
                'relationCriteria' => array (
                    'select' => '',
                    'distinct' => 'false',
                    'alias' => 't',
                    'condition' => 'where subscription_id = :sub_id {AND [where]}',
                    'order' => '{id desc, [order]}',
                    'paging' => '{[paging]}',
                    'group' => '',
                    'having' => '',
                    'join' => '',
                ),
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'columnType' => 'string',
                        'options' => array (
                            'width' => '150',
                        ),
                        'name' => 'created_on',
                        'label' => 'Date / Time',
                        'show' => false,
                        'inputMask' => '99/99/9999 99:99',
                        'stringAlias' => array (),
                    ),
                    array (
                        'name' => 'sender_id',
                        'label' => 'Sender',
                        'options' => array (
                            'if-empty' => 'System Notification',
                            'width' => '200',
                        ),
                        'columnType' => 'relation',
                        'show' => false,
                        'relParams' => array (),
                        'relCriteria' => array (
                            'select' => 't.id, fullname, r.role_name',
                            'distinct' => 'false',
                            'alias' => 't',
                            'condition' => '{[search]}',
                            'order' => '',
                            'group' => '',
                            'having' => '',
                            'join' => 'inner join p_user_role p on p.user_id = t.id
inner join p_role r on p.role_id = r.id',
                        ),
                        'relModelClass' => 'application.models.User',
                        'relIdField' => 'id',
                        'relLabelField' => '{fullname} ({role_name})',
                    ),
                    array (
                        'columnType' => 'string',
                        'options' => array (
                            'parse-func' => 'body',
                        ),
                        'name' => 'body',
                        'label' => 'Message',
                        'show' => false,
                        'inputMask' => '',
                        'stringAlias' => array (),
                    ),
                    array (
                        'columnType' => 'string',
                        'options' => array (
                            'width' => '80',
                        ),
                        'name' => 'status',
                        'label' => 'Status',
                        'show' => false,
                        'inputMask' => '',
                        'stringAlias' => array (
                            '\'2\'' => '<div class=\\\'label label-default\\\'>READ</div>',
                            '\'1\'' => '<div class=\\\'label label-success\\\'>NEW</div>',
                        ),
                    ),
                ),
                'gridOptions' => array (
                    'enablePaging' => 'true',
                    'useExternalSorting' => 'true',
                ),
                'type' => 'DataGrid',
            ),
        );
    }

}