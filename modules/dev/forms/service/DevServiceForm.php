<?php

class DevServiceForm extends DevService {

    public $content;
    public $runningInstances;
    public $stoppedInstances;
    public $view;
    public $lastRun;
    
    public static function load($id) {
        $svc = ServiceManager::getService($id);
        if (is_null($svc)) {
            return false;
        }
        $instances = [];
        $model = new DevServiceForm;
        $model->attributes = $svc;
        $model->content = file_get_contents(ServiceManager::getFilePath($svc));
        return $model;
    }

    public function getForm() {
        return array (
            'title' => 'Service Detail',
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
            'inlineJS' => 'DevServiceForm.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'name' => 'ws',
                'type' => 'WebSocketClient',
                'ctrl' => 'dev/service',
            ),
            array (
                'type' => 'Text',
                'value' => '<tabset class=\'tab-set\'>
',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- BACK BUTTON -->
<a ng-href=\"{{ Yii.app.createUrl(\'/dev/service/index\') }}\" class=\"btn btn-xs btn-default\" style=\"color:#000;font-size:11px;font-weight:bold;float:left;margin:5px 2px 0px -8px\">
    <i class=\"fa fa-caret-left\"></i> Back
</a>',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- CODE EDITOR -->
<tab ng-click=\"changeTab(\'code\')\">
    <tab-heading>
        <i id=\'code\' class=\"fa fa-code\"></i>
        {{model.command}} &bull; {{model.action}}
    </tab-heading>
</tab>',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- SERVICE LOG: START -->
<tab ng-click=\"changeTab(\'log\')\">
    <tab-heading >
        <i id=\'log\' class=\"fa fa-bars\"></i>
        Service Log
        <div
        style=\"margin:-1px -3px 0px 5px;font-size:10px;\"
        class=\"pull-right label\"
        ng-class=\"{
            \'label-success\': params.isRunning,
            \'label-default\': !params.isRunning,
        }\"
        >
            {{ params.isRunning ? 
                \'RUNNING\' :
                \'STOPPED\' }}
        </div>
    </tab-heading>
</tab>
<!-- SERVICE LOG: END -->',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- BUTTONS -->
<div class=\"btn btn-xs btn-default pull-right\" style=\"font-size:11px;font-weight:bold;margin:4px 4px 0px 0px;\" ng-click=\"popup.open()\">
    <i class=\"fa fa-pencil\"></i>
    Edit Service
</div>

<div ng-show=\"params.isRunning\" class=\"btn btn-xs btn-danger pull-right\" style=\"font-size:11px;font-weight:bold;margin:4px 4px 0px 0px;\" ng-click=\"stop()\">
    <i class=\"fa fa-stop\"></i> Stop All [ CTRL + <i class=\"fa fa fa-level-down fa-rotate-90\"></i> ]
</div>

<div ng-show=\"!params.isRunning\" class=\"btn btn-xs btn-success pull-right\" style=\"font-size:11px;font-weight:bold;margin:4px 4px 0px 0px;\" ng-click=\"start()\">
    <i class=\"fa fa-play\"></i> Run [ CTRL + <i class=\"fa fa fa-level-down fa-rotate-90\"></i> ]
</div>

<div ng-show=\"!!params.isRunning && (model.instance == \'parallel\' || (model.instance == \'single\' && model.singleInstanceMode == \'kill\')) \" class=\"btn btn-xs btn-success pull-right\" style=\"font-size:11px;font-weight:bold;margin:4px 4px 0px 0px;\" ng-click=\"start()\">
    <i class=\"fa fa-play\"></i> Run
</div>


<div class=\"btn btn-xs\" style=\"font-size:11px;font-weight:bold\" >
    {{status}}
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</tabset>',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- ACE EDITOR -->
    <div style=\'padding:0px 0px;\' ng-show=\"currentTab == \'code\'\">
        <div class=\"text-editor-builder\">
            <div class=\"text-editor\" ui-ace=\"aceConfig({onLoad:onAceLoad})\" 
style=\"position:absolute;top:27px;font-size:14px;left:0px;right:0px;bottom:0px\"
ng-model=\"model.content\">
            </div>
        </div>
    </div>
    ',
            ),
            array (
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<!-- LOG WINDOW -->
    <div style=\"margin:0px -15px;padding:10px;font-size:12px;border-bottom:1px solid #999\">
        <span>
        Instance ID: <select ng-model=\"selectedInstancePid\" ng-change=\"selInstanceChange($event)\">
            <optgroup label=\"Running Instances\">
                <option ng-value=\"i.Pid\"
                    ng-repeat=\"i in model.runningInstances\"
                >{{ i.pid }}</option>
            </optgroup>
            <optgroup label=\"Stopped Instances\">
                <option ng-value=\"i.Pid\"
                    ng-repeat=\"i in model.stoppedInstances\"
                >{{ i.pid }}</option>
            </optgroup>
        </select>
        
        &nbsp;|&nbsp; 
        <span ng-if=\"!!selectedInstance && !selectedInstance.stopTime\">
        Started {{ date(\"Y-m-d H:i:s\", selectedInstance.startTime) | timeago }}
        &nbsp;|&nbsp;
        </span>
        <span ng-if=\"!!selectedInstance && selectedInstance.stopTime\">
        Stopped at {{ date(\"Y/m/d H:i:s\", selectedInstance.stopTime) }} ({{ date(\"Y-m-d H:i:s\", selectedInstance.stopTime) | timeago }})
        </span>
        <a ng-show=\"!!selectedInstance && !selectedInstance.stopTime\" ng-url=\"sys/service/view&name={{ model.name }}&id={{selectedInstance.pid}}\" style=\"margin:-10px 0px;\"
        target=\"_blank\" class=\"btn btn-success btn-xs\">
             View Controller <i class=\"fa fa-share\"></i>
        </a>
    </div>
    
    <pre id=\"logwindow\" ng-if=\"currentTab == \'log\'\" style=\'margin:0px;border-radius:0px;position:absolute;top:63px;left:0px;right:0px;bottom:0px;overflow-y:auto;\'></pre>
',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'popup',
                'options' => array (
                    'height' => '450',
                    'width' => '600',
                ),
                'mode' => 'url',
                'url' => '/dev/service/editService&id={{ model.name }}',
                'parentForm' => 'application.modules.dev.forms.service.DevServiceForm',
            ),
        );
    }

}