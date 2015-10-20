<?php

class DevSettingsProcessManagerForm extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Settings Process Manager ',
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
            'inlineJS' => 'settingsCommandSave.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Back',
                        'icon' => 'chevron-left',
                        'options' => array (
                            'ng-href' => 'url:/dev/processManager',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div ng-if=\\"!isNewRecord\\" class=\\"separator\\"></div>',
                    ),
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'icon' => 'save',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => 'Process',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"margin-top:10px\\"></div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<input type=\"hidden\" value=\"{{params.processSettingsId}}\" name=\"processSettingsId\"/>

<input type=\"hidden\" value=\"{{params.processCommandPrefix}}\" name=\"processCommandPrefix\"/>',
            ),
            array (
                'totalColumns' => '4',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Process Name',
                        'name' => 'processName',
                        'layout' => 'Vertical',
                        'options' => array (
                            'ng-model' => 'params.processName',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Command',
                        'name' => 'processCommand',
                        'layout' => 'Vertical',
                        'prefix' => '{{ params.processCommandPrefix }}',
                        'options' => array (
                            'ng-model' => 'params.processCommand',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column3' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Period',
                        'name' => 'processPeriod',
                        'layout' => 'Vertical',
                        'options' => array (
                            'ng-model' => 'params.period',
                        ),
                        'type' => 'TextField',
                    ),
                ),
                'column4' => array (
                    array (
                        'label' => 'Period Type',
                        'name' => 'processPeriodType',
                        'options' => array (
                            'ng-model' => 'params.periodType',
                        ),
                        'defaultType' => 'first',
                        'listExpr' => '[\\"secondly\\",\\"minutely\\",\\"hourly\\",\\"daily\\"]',
                        'layout' => 'Vertical',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '25%',
                'w2' => '25%',
                'w3' => '25%',
                'w4' => '25%',
                'perColumnOptions' => array (
                    'style' => 'padding:0px 0px 0px 0px;',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\\"margin-top:10px\\"></div>',
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
                'value' => '<tabset class=\'single-tab tab-set\' ng-if=\'!!params.active\'>
<tab active=\"true\">
    <tab-heading>
        <i class=\"fa fa-cube\"></i>
        Model &bull; {{status}}
    </tab-heading>
    <div style=\'padding:0px 0px;\'>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div class=\"text-editor-builder\">
  <div class=\"text-editor\" ui-ace=\"aceConfig({
  mode: \'php\'
  })\" 
style=\"position:absolute;top:105px;font-size:14px;left:0px;right:0px;bottom:0px\"
ng-model=\"params.content\">
    </div>
</div>
',
            ),
            array (
                'type' => 'Text',
                'value' => '    </div>
</tab>
</tabset>',
            ),
        );
    }

}