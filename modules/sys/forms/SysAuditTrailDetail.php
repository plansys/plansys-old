<?php

class SysAuditTrailDetail extends AuditTrail {
    
    public function getFields() {
        return array (
            array (
                'linkBar' => array (),
                'title' => 'Audit Trail Detail #{{model.id}}',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Page Title',
                        'name' => 'description',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Action',
                        'name' => 'type',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Pathinfo',
                        'name' => 'pathinfo',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Params',
                        'name' => 'params',
                        'type' => 'LabelField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Url',
                        'name' => 'url',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module',
                        'name' => 'module',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Controller',
                        'name' => 'ctrl',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Action',
                        'name' => 'action',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Stamp',
                        'name' => 'stamp',
                        'fieldType' => 'datetime',
                        'fieldOptions' => array (
                            'disabled' => 'true',
                        ),
                        'type' => 'DateTimePicker',
                    ),
                    array (
                        'label' => 'User',
                        'name' => 'user_id',
                        'fieldOptions' => array (
                            'disabled' => 'true',
                        ),
                        'modelClass' => 'application.models.User',
                        'idField' => 'id',
                        'labelField' => 'fullname',
                        'type' => 'RelationField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Data',
                'type' => 'SectionHeader',
            ),
            array (
                'value' => '<table class=\"table table-condensed\" style=\"margin-top:10px;\">
    <tr ng-repeat=\"(key,value) in data\">
        <th style=\"width:20%\">{{key}}</th>
        <td>
            {{ value }}
        </td>
    </tr>
</table>',
                'type' => 'Text',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Audit Trail Detail',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'audit-detail.js',
        );
    }

}