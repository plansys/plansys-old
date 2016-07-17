<?php

class DevService extends Form {

    public $name;
    public $commandPath = '';
    public $command; 
    public $action = 'Index';
    public $schedule = 'manual';
    public $period;
    public $instance = 'single';
    public $singleInstanceMode = 'wait';
    public $status = 'draft';
    private $isNewRecord = true;
    
    public function rules() {
        return [
            ['name, commandPath, command , action', 'required'],
            ['name', 'serviceValidator']
        ];
    }
    
    public function serviceValidator() {
        $svc = ServiceSetting::get('list.' .$this->name);
        if ($this->isNewRecord && (count($svc) > 1)) {
            $this->addError('name', 'Service name already exists, choose another name');
        }
    }

    public static function load($id) {
        $svc = ServiceSetting::get('list.' . $id);
        if (is_null($svc)) {
            return false;
        }
        $model = new DevService;
        $model->attributes = $svc;
        $model->isNewRecord = false;
        return $model;
    }

    public function getFilePath() {
        return Yii::getPathOfAlias($this->commandPath.".".$this->command) . ".php";
    }

    public static function getModuleList() {
        $rawList = ModuleGenerator::listModuleForMenuTree();
        
        $modules = [
            ''=> '-- Choose Module --',
            '---' => '---'
        ];
        foreach($rawList as $k=>$list) {
            $m = $list['module'] == 'plansys' ? 'application' : $list['module'];
            $modules[$m . ".commands"] = ucfirst($list['label']);
        }

        foreach($rawList as $k=>$list) {
            $modules[$list['label'] . " Module"] = []; 
            foreach ($list['items'] as $j=>$l) {
                $l['module'] = $l['module'] == 'plansys' ? 'application' : $l['module'];
                
                $modules[$list['label'] . " Module"][$l['module'] . ".modules." . $j . ".commands"] = ucfirst($j);
            }
        }
        return $modules;
    }

    public function save() {
        if ($this->isNewRecord) {
            $this->create();
        } else {
            ServiceSetting::set('list.' . $this->name, $this->attributes);
        }
    }
    
    public function create() {
        
        ## Prepare Command Class Directory
        $path = Yii::getPathOfAlias($this->commandPath);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        ## Write New Command Class
        $filePath = $path . DIRECTORY_SEPARATOR . $this->command . "Command.php";
        if (!is_file($filePath)) {
            $content = <<<EOF
<?php

class {$this->command}Command extends Service {
    public function actionIndex() {
        ## Put your code here
        
    }
}
EOF;
            file_put_contents($filePath, $content);
        }
        
        if (substr($this->command, -8) != "Command") {
            $this->command = $this->command . "Command";
        }

        if (substr($this->action, 8) != "action") {
            $this->action = "action" . $this->action ;
        }
        
        ## Setup Process Entry in Setting            
        ServiceSetting::set("list.".$this->name, $this->attributes);
    }

    public function getForm() {
        return array (
            'title' => 'Service Manager',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'DevService.js',
            'options' => array (),
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save Service',
                        'buttonType' => 'success',
                        'icon' => 'check',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                            'ng-if' => '!!model.name && !!model.command && !!model.action',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '{{ params.isNewRecord ? \'New Service\' : \'Edit Service\' }}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Service Name',
                        'name' => 'name',
                        'options' => array (
                            'ng-change' => 'onNameChange()',
                        ),
                        'fieldOptions' => array (
                            'ng-disabled' => '!params.isNewRecord',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Module',
                        'name' => 'commandPath',
                        'options' => array (
                            'ng-change' => 'onModuleChange()',
                            'ng-if' => 'params.isNewRecord',
                        ),
                        'listExpr' => 'DevService::getModuleList();',
                        'searchable' => 'Yes',
                        'otherLabel' => 'New',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Path',
                        'name' => 'commandPath',
                        'options' => array (
                            'ng-if' => '!params.isNewRecord',
                        ),
                        'fieldOptions' => array (
                            'disabled' => 'true',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Command',
                        'name' => 'command',
                        'options' => array (
                            'ng-if' => '!params.isNewRecord',
                        ),
                        'fieldOptions' => array (
                            'disabled' => 'true',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Command',
                        'name' => 'command',
                        'options' => array (
                            'ng-if' => '!$newCommand && !!listCommand',
                            'ps-list' => 'listCommand',
                            'ng-change' => 'onCommandChange()',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Action',
                        'name' => 'action',
                        'options' => array (
                            'ng-if' => '(!$newCommand && !!listAction) || !params.isNewRecord',
                            'ps-list' => 'listAction',
                        ),
                        'listExpr' => '[\'action\']',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\"text-center\"
     ng-show=\"params.isNewRecord && !$newCommand && !model.action && !!model.commandPath\">
    
    <div style=\"height:40px;line-height:40px\">
        &mdash; OR &mdash;
    </div>
    
    <div ng-click=\"$newCommand = true\" 
         class=\"btn btn-xs btn-default\">
        <i class=\"fa fa-plus\"></i> Add New Command
    </div>
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"!!$newCommand\\">',
                    ),
                    array (
                        'label' => 'Command',
                        'name' => 'command',
                        'postfix' => 'Command',
                        'options' => array (
                            'ng-change' => 'model.command = formatClass(model.command)',
                        ),
                        'fieldOptions' => array (
                            'style' => 'text-align:right',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Action',
                        'name' => 'action',
                        'prefix' => 'action',
                        'options' => array (
                            'ng-change' => 'model.action= formatClass(model.action)',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\"text-center\"
     ng-show=\"params.isNewRecord && !!$newCommand && !model.action && !!listCommand\">
    
    <div style=\"height:40px;line-height:40px\">
        &mdash; OR &mdash;
    </div>
    
    <div ng-click=\"$newCommand = false\" 
         class=\"btn btn-xs btn-default\">
        <i class=\"fa fa-check\"></i> Choose Existing Command
    </div>
</div>
',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-show=\\"model.commandPath != \'\' && !!model.command && !!model.action\\">',
                    ),
                    array (
                        'label' => 'Run Schedule',
                        'name' => 'schedule',
                        'defaultType' => 'first',
                        'listExpr' => '[
\'manual\'=>\'Manual\',
\'second\'=>\'Every X Second(s)\',
\'minute\'=>\'Every X Minute(s)\',
\'hour\'=>\'Every X Hour(s)\',
\'day\'=>\'Every X Day(s)\'
]',
                        'fieldWidth' => '5',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Run Period',
                        'name' => 'period',
                        'prefix' => 'Every',
                        'postfix' => '{{ model.schedule }}',
                        'options' => array (
                            'ng-show' => 'model.schedule != \'manual\'',
                        ),
                        'fieldOptions' => array (
                            'style' => 'text-align:center;',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<hr/>',
                    ),
                    array (
                        'label' => 'Run Instance',
                        'name' => 'instance',
                        'defaultType' => 'first',
                        'listExpr' => '[
\'single\'=>\'Single Instance\',
\'parallel\'=>\'Parallel Instance\']',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'If instance is still running:',
                        'name' => 'singleInstanceMode',
                        'options' => array (
                            'ng-if' => 'model.instance == \'single\'',
                        ),
                        'defaultType' => 'first',
                        'listExpr' => '[
\'wait\'=>\'Do not run process\',
\'kill\'=>\'Kill running instance and run process\'
]',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\"!$newCommand\" class=\"alert alert-warning text-center\" style=\"margin-top:20px\">
    <div ng-click=\"deleteService();\" class=\"btn btn-danger btn-sm\"><i class=\"fa fa-warning\"></i> Delete This Service
    </div>
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'options' => array (
                    'style' => 'height:400px;',
                ),
                'perColumnOptions' => array (
                    'style' => 'padding:5px',
                ),
                'type' => 'ColumnField',
            ),
        );
    }

}