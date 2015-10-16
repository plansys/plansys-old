<?php

class DevSettingsProcessManager extends Form {

    public function getForm() {
        return array (
            'title' => 'Settings Process Manager',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.Setting',
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
            'inlineJS' => 'settingsProcessManager.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Stop Process Manager',
                        'buttonType' => 'danger',
                        'icon' => 'stop',
                        'options' => array (
                            'href' => 'url:/dev/processManager/stop',
                            'ng-if' => 'params.pmIsRunning',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Start Process Manager',
                        'buttonType' => 'success',
                        'icon' => 'play',
                        'options' => array (
                            'href' => 'url:/dev/ProcessManager/start',
                            'ng-if' => '!params.pmIsRunning',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"!isNewRecord\\" class=\\"separator\\"></div>',
                    ),
                    array (
                        'label' => 'New Process',
                        'buttonType' => 'info',
                        'icon' => 'plus',
                        'options' => array (
                            'ng-click' => 'procManPopUp()',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => 'Process Manager',
                'showSectionTab' => 'No',
                'showOptionsBar' => 'Yes',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataSourceProcMan',
                'fieldType' => 'php',
                'php' => 'ProcessHelper::listAllCmdForGridView();',
                'type' => 'DataSource',
            ),
            array (
                'type' => 'GridView',
                'name' => 'gridViewProcMan',
                'label' => 'GridView',
                'datasource' => 'dataSourceProcMan',
                'columns' => array (
                    array (
                        'name' => 'file',
                        'label' => 'File',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'name',
                        'label' => 'Name',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'command',
                        'label' => 'Command',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'period',
                        'label' => 'Period',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'periodType',
                        'label' => 'Period Type',
                        'html' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'lastRun',
                        'label' => 'Last Run',
                        'options' => array (),
                        'mergeSameRow' => '',
                        'mergeSameRowWith' => '',
                        'html' => '<td ng-class=\"rowClass(row, \'lastRun\', \'string\')\">
    {{ date(\"d M Y H:i:s\", row.lastRun) }}
</td>',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => false,
                        'cellMode' => 'custom',
                    ),
                    array (
                        'name' => 'isStarted',
                        'label' => 'Status',
                        'html' => '<td ng-class=\"rowClass(row, \'isStarted\', \'string\')\">
    <span ng-if=\"row.isStarted\" class=\"label label-success\">Started</span>
    <span ng-if=\"!row.isStarted\" class=\"label label-danger\">Stopped</span>
</td>',
                        'columnType' => 'string',
                        'show' => false,
                        'cellMode' => 'custom',
                    ),
                    array (
                        'name' => 'action',
                        'label' => 'Action',
                        'options' => array (),
                        'mergeSameRow' => '',
                        'mergeSameRowWith' => '',
                        'html' => '<td ng-class=\"rowClass(row, \'action\', \'string\')\" align=\"center\">
    <span ng-if=\"params.pmIsRunning\">
        <a ng-if=\"!row.isStarted\" ng-href=\"{{url(\'/dev/processManager/startProcess&id=\'+row.id)}}\" class=\"btn btn-xs btn-success\"> <i class=\"fa fa-play\"></i></a>
        <a ng-if=\"row.isStarted\" ng-href=\"{{url(\'/dev/processManager/stopProcess&id=\'+row.id)}}\" class=\"btn btn-xs btn-danger\"> <i class=\"fa fa-stop\"></i></a>
    </span>
    <a ng-href=\"{{url(\'/dev/processManager/update&id=\'+row.id+\'&active=\'+row.file)}}\" class=\"btn btn-xs btn-primary\"> <i class=\"fa fa-pencil\"></i></a>
    <a ng-href=\"{{url(\'/dev/processManager/delete&id=\'+row.id)}}\" class=\"btn btn-xs btn-danger\" onClick=\"return confirm(\'Are you sure ?\')\"> <i class=\"fa fa-trash\"></i></a>
    
</td>',
                        'columnType' => 'string',
                        'typeOptions' => array (
                            'string' => array (
                                'html',
                            ),
                        ),
                        'show' => true,
                        'cellMode' => 'custom',
                    ),
                ),
            ),
        );
    }

}