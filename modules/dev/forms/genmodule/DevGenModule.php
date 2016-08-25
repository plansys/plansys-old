<?php

class DevGenModule extends Form {
    ## MODULE INFO VARS
    public $name;
    public $alias;
    public $path;
    public $classPath;
    public $module;
    public $imports = '';
    public $error = '';
    public $synced = '';

    ## ACCESS CONTROL VARS
    public $accessType = 'DEFAULT';
    public $defaultRule = 'deny';
    public $rolesRule = [];
    public $usersRule = [];
    public $acSource = '';

    public function checkSync() {
        $a = trim($this->module->removeIndent($this->module->generateImport()));
        $b = trim($this->module->loadImport());

//        $diff = TextDiff::compare($a, $b);
        $this->synced = $a != $b;
        return $this->synced;
    }

    public function create($module) {
        $m = explode(".", $module);
        if (count($m) == 2) {
            $name = lcfirst($m[1]);
            $class = ucfirst($name) . "Module";
            $basePath = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $alias = ($m[0] == "app" ? 'app' : 'application') . ".modules.{$name}.{$class}";
            $path = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";

            $this->name = $name;
            $this->alias = $alias;
            $this->path = $path;
            $this->classPath = $classPath;

            if (!is_file($this->classPath)) {
                $this->module = ModuleGenerator::init($alias, 'create');
                if (is_null($this->module)) {
                    $this->error = 'Failed to create module
Invalid Class Name "' . $name . '"';
                } else {
                    $this->imports = $this->module->loadImport();
                }
            } else {
                $this->module = null;
                $this->error = 'Failed to create module
Module "' . $name . '" already exist';
            }
        }
    }

    public function delete() {
        $dirPath = $this->path;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            $path->isDir() && !$path->isLink() ? rmdir($path->getPathname()) : unlink($path->getPathname());
        }
        rmdir($dirPath);
    }


    public function load($module) {
        $m = explode(".", $module);
        if (count($m) == 2 && $m[1] != '') {
            $name = lcfirst($m[1]);
            $class = ucfirst($name) . "Module";
            $basePath = $m[0] == "app" ? Setting::getAppPath() : Setting::getApplicationPath();
            $alias = ($m[0] == "app" ? 'app' : 'application') . ".modules.{$name}.{$class}";
            $path = $basePath . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $name;
            $classPath = $path . DIRECTORY_SEPARATOR . $class . ".php";

            $this->name = $name;
            $this->alias = $alias;
            $this->path = $path;
            $this->classPath = $classPath;

            if (is_file($this->classPath)) {
                $this->module = ModuleGenerator::init($alias, 'load');
                $this->accessType = $this->module->checkAccessType();
                $this->defaultRule = $this->module->defaultRule;
                $this->rolesRule = $this->module->rolesRule;
                $this->usersRule = $this->module->usersRule;
                $this->acSource = $this->module->acSource;
                $this->imports = $this->module->loadImport();
            } else {
                $this->module = null;
            }
        }
    }

    public function getControllers() {
        if (isset($this->module)) {
            return $this->module->getControllers();
        } else {
            return null;
        }
    }

    public function getForm() {
        return array(
            'title'     => 'Generate Module',
            'layout'    => array(
                'name' => '2-cols',
                'data' => array(
                    'col1' => array(
                        'size'        => '200',
                        'sizetype'    => 'px',
                        'type'        => 'menu',
                        'name'        => 'col1',
                        'file'        => 'application.modules.dev.menus.GenModule',
                        'title'       => 'Module',
                        'icon'        => 'fa-empire',
                        'inlineJS'    => 'GenModule.js',
                        'menuOptions' => array(),
                    ),
                    'col2' => array(
                        'type'     => 'mainform',
                        'name'     => 'col2',
                        'sizetype' => '%',
                    ),
                ),
            ),
            'inlineJS'  => 'GenModule.js',
            'includeJS' => array(),
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<!-- EMPTY MODULE -->
<div ng-if=\'!model.name\'>
    <div class=\"empty-box-container\">
        <div class=\"message\">
            Please select item on right sidebar
        </div>
    </div>

</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!------------------------- MODULE INFO TAB ------------------------>
<tabset class=\'tab-set\' ng-if=\'model.name\'>
<tab heading=\"Module Info\" select=\'setTab(1)\'>',
            ),
            array (
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Module Name',
                        'name' => 'name',
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module Alias',
                        'name' => 'alias',
                        'type' => 'LabelField',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Class Path',
                        'name' => 'classPath',
                        'labelOptions' => array (
                            'style' => 'text-align:left;',
                        ),
                        'type' => 'LabelField',
                    ),
                    array (
                        'label' => 'Module Directory',
                        'name' => 'path',
                        'labelOptions' => array (
                            'style' => 'text-align:left;',
                        ),
                        'type' => 'LabelField',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'title' => '<i class=\'fa fa-empire\'></i> Import Initialization <span ng-bind-html=\'importStatus\'></span>',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'label' => 'Generate Import',
                        'buttonType' => 'success',
                        'icon' => 'refresh',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;
margin:-50px -45px 0px 0px;',
                            'href' => 'url:/dev/genModule/genImport?active={params.active}',
                            'confirm' => 'WARNING: Current code will be REPLACED. Are you sure?',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '
<div style=\'margin:-25px -50px -25px -40px;\'>
    <div class=\"source-editor\"
     style=\"width:100%;
     height:400px;\"
     ng-model=\"model.imports\"
     ng-change=\"saveImport()\"
     ng-delay=\"500\"
     ui-ace=\"aceConfig({inline:true})\"></div>
</div>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'title' => '<i class=\'fa fa-cubes\'></i> Controllers',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'label' => 'Add New Controller',
                        'buttonType' => 'success',
                        'icon' => 'plus-circle',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;
margin:-50px -45px 0px 0px;',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<table class=\"table table-condensed table-bordered table-small\">
    
    <tr ng-repeat=\"c in params.controllers track by $index\">
        <td>{{ c.class }}</td>
    </tr>
</table>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<!--------------------- ACCESS CONTROL TAB ------------------------->
</tab><tab select=\'setTab(2)\'>
<tab-heading>
Access Control <span ng-bind-html=\'acStatus\'></span>
</tab-heading>',
            ),
            array (
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Access Control type',
                        'name' => 'accessType',
                        'onLabel' => 'DEFAULT',
                        'offLabel' => 'CUSTOM',
                        'options' => array (
                            'ng-change' => 'saveAC();',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"model.accessType == \'DEFAULT\'\\">',
                    ),
                    array (
                        'label' => 'Default Access Rule',
                        'name' => 'defaultRule',
                        'options' => array (
                            'ng-change' => 'saveAC();',
                        ),
                        'labelOptions' => array (
                            'style' => 'text-align:left;',
                        ),
                        'list' => array (
                            'deny' => 'Deny',
                            'allow' => 'Allow',
                        ),
                        'fieldWidth' => '4',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\'info\'>
    <i class=\"fa fa-info-circle\"></i>
    This value will be used when no matching rule</div>',
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
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- DEFAULT ACCESS TYPE -->
<div ng-if=\"model.accessType == \'DEFAULT\'\">',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"margin:0px -15px\\">',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'title' => '<i class=\'fa fa-user-md\'></i> Role Access',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'name' => 'rolesRule',
                        'fieldTemplate' => 'form',
                        'templateForm' => 'application.modules.dev.forms.genmodule.DevGenModuleAccess',
                        'layout' => 'Vertical',
                        'options' => array (
                            'ng-change' => 'saveAC();',
                            'unique' => '[\'role\', \'access\']',
                            'class' => 'flat',
                            'ng-delay' => '500',
                        ),
                        'sortable' => 'No',
                        'singleViewOption' => array (
                            'name' => 'val',
                            'fieldType' => 'text',
                            'labelWidth' => 0,
                            'fieldWidth' => 12,
                            'fieldOptions' => array (
                                'ng-delay' => 500,
                            ),
                        ),
                        'type' => 'ListView',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'title' => '<i class=\'fa fa-user\'></i> User Access',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'name' => 'usersRule',
                        'fieldTemplate' => 'form',
                        'templateForm' => 'application.modules.dev.forms.genmodule.DevGenModuleAccess',
                        'layout' => 'Vertical',
                        'options' => array (
                            'ng-change' => 'saveAC();',
                            'unique' => '[\'user\', \'access\']',
                            'class' => 'flat',
                            'ng-delay' => '500',
                        ),
                        'sortable' => 'No',
                        'singleViewOption' => array (
                            'name' => 'val',
                            'fieldType' => 'text',
                            'labelWidth' => 0,
                            'fieldWidth' => 12,
                            'fieldOptions' => array (
                                'ng-delay' => 500,
                            ),
                        ),
                        'type' => 'ListView',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'w3' => '33%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr style=\\"margin:0px -15px;\\"/>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- CUSTOM ACCESS TYPE -->
</div><div ng-if=\"model.accessType == \'CUSTOM\'\">',
            ),
            array (
                'title' => 'Access Control Function',
                'type' => 'SectionHeader',
            ),
            array (
                'type' => 'Text',
                'value' => '
<div style=\'margin:0px -15px;\'>
    <div class=\"source-editor\"
     style=\"width:100%;
     height:400px;\"
     ng-model=\"model.acSource\"
     ng-change=\"saveAC()\"
     ng-delay=\"500\"
     ui-ace=\"aceConfig({inline:true})\"></div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- TAB CLOSER -->
</div></tab></tabset>',
            ),
        );
    }

}