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
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Data',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '<table class=\"table table-condensed table-bordered table-striped\" style=\"margin-top:10px;\">
    <tr ng-repeat=\"(key,value) in data\">
        <th style=\"width:20%\">{{key}}</th>
        <td>
            {{ value }}
        </td>
    </tr>
</table>
',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"isRelated\\">',
            ),
            array (
                'title' => 'Relational Data',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '
    <table class=\"table table-condensed table-striped table-bordered\" style=\"margin-top:10px;\">
        <tr ng-repeat=\"(key,value) in relations\">
            <th style=\"width:20%\">{{key}}</th>
            <td> 
                <table class=\"table table-condensed table-bordered\">
                    <tr ng-repeat=\"(k,v) in value\">
                        <th>{{k}}</th>
                        <td>
                            
                            <div ng-if=\"isObject(v)\">
                                <table class=\"table table-bordered\">
                                    <tr ng-repeat=\"(h,i) in v\">
                                        <th style=\"width:30%;padding:3px;\">{{h}}</th>
                                        <td style=\"padding:3px;\">{{i}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div ng-if=\"!isObject(v)\">
                                {{ v }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\\"currentModel.length > 0\\">',
            ),
            array (
                'title' => 'Cached Data List',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '
<table class=\"table table-condensed table-striped table-bordered\" style=\"margin-top:10px;\">
    <tr ng-repeat=\"(key,value) in currentModel\">
        <th style=\"width:30px;text-align:center;\">{{key}}</th>
        <td> 
            <table class=\"table table-condensed table-bordered\">
                <tr ng-repeat=\"(k,v) in value\">
                    <th style=\"width:20%;padding:3px;\">{{k}}</th>
                    <td style=\"padding:3px;\">
                        
                        <div ng-if=\"isObject(v)\">
                            <table class=\"table table-bordered\">
                                <tr ng-repeat=\"(h,i) in v\">
                                    <th style=\"width:20%;padding:3px;\">{{h}}</th>
                                    <td style=\"padding:3px;\">{{i}}</td>
                                </tr>
                            </table>
                        </div>
                        <div ng-if=\"!isObject(v)\">
                            {{ v }}
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</div>',
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