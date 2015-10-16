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
                'title' => 'Create new process',
                'showSectionTab' => 'No',
                'showOptionsBar' => 'Yes',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"margin-top:15px\"></div>
<input type=\"hidden\" name=\"processFile\" value=\"{{model.processUrl}}\"/>',
            ),
            array (
                'label' => 'Name',
                'name' => 'processName',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Command',
                'name' => 'processUrl',
                'listExpr' => 'ProcessHelper::listCmdForMenuTree();',
                'searchable' => 'Yes',
                'showOther' => 'Yes',
                'otherLabel' => 'New',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Process Command Line',
                'name' => 'processCommand',
                'type' => 'TextField',
            ),
        );
    }

}