<?php

class DevServiceIndex extends Form {

    public $status = 'Service Daemon Stopped';

    public function getForm() {
        return array (
            'title' => 'Service Manager',
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
            'inlineJS' => 'DevServiceIndex.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'name' => 'status',
                        'onLabel' => 'Service Daemon Running',
                        'offLabel' => 'Service Daemon Stopped',
                        'options' => array (
                            'style' => 'display:inline-block;margin: 0px -5px -5px 0px;',
                            'ng-change' => 'daemonChange()',
                        ),
                        'fieldOptions' => array (
                            'ng-disabled' => 'true',
                        ),
                        'size' => 'small',
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'label' => 'New Service',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'ng-click' => 'popupWindowCreateProcess.open()',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div class=\\"separator\\"></div>',
                    ),
                ),
                'title' => 'Service Manager',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'popupWindowCreateProcess',
                'options' => array (
                    'height' => '450',
                    'width' => '600',
                ),
                'mode' => 'url',
                'url' => '/dev/service/create',
                'title' => 'Create New Process',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-show=\\"model.status == \'Service Daemon Running\'\\">',
            ),
            array (
                'name' => 'dataSourceProcMan',
                'fieldType' => 'php',
                'php' => 'ServiceManager::getAllServices();',
                'type' => 'DataSource',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridViewProcMan',
                'label' => 'GridView',
                'datasource' => 'dataSourceProcMan',
                'gridOptions' => array (
                    'pageSize' => '999',
                    'controlBar' => 'false',
                ),
                'columns' => array (
                    array (
                        'name' => 'name',
                        'label' => 'Service Name',
                        'html' => '<td ng-class=\"rowClass(row, \'name\', \'string\')\" style=\"width:50%\">
    <div  ng-include=\'\"row-state-template\"\'></div>
    <span class=\'row-group-padding\' ng-if=\'!!row.$level\'
        style=\'width:{{row.$level*10}}px;\'></span>
    <div ng-if=\"row.is_plansys\" class=\"label label-warning\"><i class=\"fa fa-warning\"></i> Plansys Service</div> {{row.name}}
</td>',
                        'columnType' => 'string',
                        'show' => true,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'custom',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'command',
                        'label' => 'Command',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'action',
                        'label' => 'Action',
                        'html' => '<td ng-class=\"rowClass(row, \'action\', \'string\')\">
    {{row.action}}
</td>',
                        'columnType' => 'string',
                        'show' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'default',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'schedule',
                        'label' => 'Run Schedule',
                        'options' => array (),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'html' => '<td ng-class=\"rowClass(row, \'schedule\', \'string\')\" style=\"text-align:center\">
    {{row.schedule}}
</td>',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => false,
                        'cellMode' => 'custom',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'status',
                        'label' => 'Status',
                        'html' => '<td ng-class=\"rowClass(row, \'status\', \'string\')\" style=\"text-align:center;\">
    <div ng-if=\"row.status == \'running\'\" class=\"label label-success\">RUNNING: {{row.running_instances}} PROCESS</div>
    <div ng-if=\"row.status == \'stopped\'\" class=\"label label-default\">STOPPED</div>
    <div ng-if=\"row.status == \'draft\'\" class=\"label label-danger\" tooltip-html-unsafe=\"DRAFT service will not run.<hr/> To remove DRAFT status, you must manually run this service and make sure there is no error in your code.\"><i class=\"fa fa-warning\"></i> DRAFT</div>
</td>',
                        'columnType' => 'string',
                        'show' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'custom',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'last_run',
                        'label' => 'Last Run',
                        'html' => '<td ng-class=\"rowClass(row, \'last_run\', \'string\')\" style=\"white-space:nowrap;\">
    {{ row.last_run }}
</td>',
                        'columnType' => 'string',
                        'show' => false,
                        'mergeSameRow' => 'No',
                        'cellMode' => 'custom',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                    array (
                        'name' => 'action',
                        'label' => 'Action',
                        'options' => array (),
                        'mergeSameRow' => 'No',
                        'mergeSameRowWith' => '',
                        'html' => '<td ng-class=\"rowClass(row, \'action\', \'string\')\" style=\"white-space:nowrap;text-align:center;\">
    <a ng-url=\"/dev/service/update&id={{row.name}}\" class=\"btn btn-xs btn-info\">
        <i class=\"fa fa-pencil\"></i>
        Edit Code
    </a>
</td>',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => false,
                        'cellMode' => 'custom',
                        '$listViewName' => 'columns',
                        '$showDF' => false,
                    ),
                ),
            ),
            array (
                'type' => 'Text',
                'value' => '</div>
<div ng-if=\"model.status != \'Service Daemon Running\'\"
    class=\"alert alert-danger text-center\" style=\"margin:50px auto;width:500px\">
        <b>Please Run Service Daemon</b>
        <br/><br/>
        You must start service daemon before<br/>
        running or editing existing services. 
    </div>',
            ),
        );
    }

}