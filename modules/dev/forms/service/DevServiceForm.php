<?php

class DevServiceForm extends DevService {

    public $content;

    public static function load($id) {
        $svc = Setting::get('services.list.' . $id);
        if (is_null($svc)) {
            return false;
        }
        $instances = [];
        $model = new DevServiceForm;
        $model->attributes = $svc;
        $model->content = file_get_contents($model->filePath);
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
                'type' => 'Text',
                'value' => '<tabset class=\'tab-set\'>
',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- BACK BUTTON -->
<a ng-href=\"{{ Yii.app.createUrl(\'/dev/service/index\') }}\" class=\"btn btn-xs btn-default\" style=\"color:#000;font-size:11px;font-weight:bold;float:left;margin:5px 2px 0px -8px\">
    <i class=\"fa fa-caret-left\"></i> Back
</a>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- CODE EDITOR -->
<tab select=\"changeTab(\'code\')\">
    <tab-heading>
        <i id=\'code\' class=\"fa fa-code\"></i>
        {{model.command}} &bull; {{model.action}}
    </tab-heading>
</tab>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- SERVICE LOG: START -->
<tab select=\"changeTab(\'log\')\">
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
    {{status}} {{ $parent.tab | json }}
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '</tabset>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- ACE EDITOR -->
    <div style=\'padding:0px 0px;\' ng-show=\"tab == \'code\'\">
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
                'type' => 'Text',
                'value' => '<!-- LOG WINDOW -->
    <div style=\"margin:0px -15px;padding:10px;font-size:12px;border-bottom:1px solid #999\">
        <span ng-if=\"params.isRunning\">
        Instance ID: <select ng-model=\"selectedInstance\">
            <option value=\"{{ i.id }}\"
                ng-repeat=\"i in instances\"
            >{{ i.id }}</option>
        </select>
        &nbsp;|&nbsp;
        </span>
        Max Lines: <select ng-model=\"maxLines\">
            <option value=\"5\">5</option>
            <option value=\"10\">10</option>
            <option value=\"15\">15</option>
            <option value=\"20\">20</option>
            <option value=\"25\">25</option>
            <option value=\"35\">35</option>
            <option value=\"50\">50</option>
        </select>
        &nbsp;|&nbsp; 
        <a ng-show=\"params.isRunning\" ng-url=\"sys/service/view&name={{ model.name }}&id={{selectedInstance}}\"
        target=\"_blank\" class=\"btn btn-success btn-xs\">
             View Controller <i class=\"fa fa-share\"></i>
        </a>
    </div>
    
    <pre ng-if=\"tab == \'log\'\">{{log }}</pre>
',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'popup',
                'options' => array (
                    'height' => '400',
                    'width' => '600',
                ),
                'mode' => 'url',
                'url' => '/dev/service/editService&id={{ model.name }}',
            ),
        );
    }

}