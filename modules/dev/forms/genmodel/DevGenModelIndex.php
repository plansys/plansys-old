<?php

class DevGenModelIndex extends Form {
    ## MODEL INFO VARS

    public $name;
    public $alias;
    public $path;
    public $classPath;
    public $rules;
    public $relations;

    ## GENERATOR VARS
    public $generator;
    public $mode = 'Normal';
    public $imports = '';
    public $error = '';
    public $synced = '';

    public function getForm() {
        return array (
            'title' => 'Generate Model',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenModel',
                        'title' => 'Model',
                        'icon' => 'fa-cube',
                        'inlineJS' => 'GenModel.js',
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
            'inlineJS' => 'indexModel.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<style>
    tab-heading {
        margin: -4px -8px -5px -8px;
        padding: 4px 8px 5px 8px;
        height: 23px;
    }
    
</style>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- EMPTY MODULE -->
<div ng-if=\'!params.active\'>
    <div class=\"empty-box-container\">
        <div class=\"message\">
            Please select item on right sidebar
        </div>
    </div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '
<tabset class=\'single-tab tab-set\' ng-if=\'!!params.active\'>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- TAB MODEL CODE -->
<tab active=\"tabCode\">
    <tab-heading ng-click=\"tabSelect()\">
        <i class=\"fa fa-code\"></i>
        {{params.name}} Code 
        <span ng-if=\"!!tabCode\">
            &bull; {{status}}
        </span>
    </tab-heading>
    <div style=\'padding:0px 0px;\'>
        ',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"text-editor-builder\">
  <div class=\"text-editor\" ui-ace=\"aceConfig({
  mode: \'php\'
  })\" 
style=\"position:absolute;top:28px;font-size:14px;left:0px;right:0px;bottom:0px\"
ng-model=\"params.content\">
    </div>
</div>
',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</tab>
',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- TAB Rel -->
<tab active=\"tabRel\">
    <tab-heading ng-click=\"tabRelSelect()\">
       <span> <i class=\"fa fa-link\"></i>
           Relations
           <span ng-if=\"!!tabRel\">
               &bull; {{status}}
           </span></span>
    </tab-heading>
    <div style=\'padding:0px 0px;\'>',
            ),
            array (
                'name' => 'tabRelations',
                'subForm' => 'application.modules.dev.forms.genmodel.DevGenModelRelations',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</tab>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- TAB RULES -->
<tab active=\"tabRules\" style=\"display:none;\">
    <tab-heading ng-click=\"tabRulesSelect();\">
        <i class=\"fa fa-unlock-alt\"></i>
        Rules
        <span ng-if=\"!!tabRules\">
            &bull; {{status}}
        </span>
    </tab-heading>
    <div style=\'padding:0px 0px;\'>',
            ),
            array (
                'name' => 'tabRules',
                'subForm' => 'application.modules.dev.forms.genmodel.DevGenModelRules',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</tab>',
            ),
            array (
                'type' => 'Text',
                'value' => '<!-- TAB IMPORTER -->
<tab active=\"tabImporter\">
    <tab-heading ng-click=\"tabImporterSelect();\">
        <i class=\"fa fa-upload\"></i>
        Importer
        <span ng-if=\"!!tabImporter\">
            &bull; {{status}}
        </span>
    </tab-heading>
    <div style=\'padding:0px 0px;\'>
',
            ),
            array (
                'name' => 'tabImporter',
                'subForm' => 'application.modules.dev.forms.genmodel.DevGenModelImporter',
                'type' => 'SubForm',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</tab>',
            ),
            array (
                'type' => 'Text',
                'value' => '

</tabset>',
            ),
        );
    }

}