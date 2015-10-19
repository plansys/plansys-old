<?php

class DevSettingsProcessManagerPopUp extends Form {

    public function getForm() {
        return array (
            'title' => 'Settings Process Manager Pop Up',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'settingsProcessManagerPopPup.js',
            'options' => array (),
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
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
                'title' => 'Create New Process',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"margin-top:15px\"></div>
<input type=\"hidden\" name=\"processFile\" value=\"{{model.processUrl}}\"/>',
            ),
            array (
                'label' => 'Command',
                'name' => 'processUrl',
                'options' => array (
                    'ng-change' => 'processUrlChange()',
                ),
                'listExpr' => 'ProcessHelper::listCmdForMenuTree();',
                'searchable' => 'Yes',
                'otherLabel' => 'New',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Process Name',
                'name' => 'processName',
                'options' => array (
                    'ng-if' => '!!model.processUrl',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Process Command Line',
                'name' => 'processCommand',
                'prefix' => '{{ prefix }}',
                'options' => array (
                    'ng-if' => '!!model.processUrl',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Period',
                'name' => 'processPeriod',
                'options' => array (
                    'ng-if' => '!!model.processUrl',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Period Type',
                'name' => 'processPeriodType',
                'options' => array (
                    'ng-if' => '!!model.processUrl',
                ),
                'defaultType' => 'first',
                'listExpr' => '[\\"secondly\\",\\"minutely\\",\\"hourly\\",\\"daily\\"]',
                'type' => 'DropDownList',
            ),
        );
    }

}