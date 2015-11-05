<?php

class SysServiceForm extends Form {

    public function getForm() {
        return array (
            'title' => 'Detail Service ',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'SysServiceForm.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div class=\"panel panel-default\"
    style=\"width:500px;margin:40px auto;\">
    <div class=\"panel-body\">
        <h3 style=\"margin:0px;\">
            <div ng-bind-html=\"params.title\"></div>
        </h3>
        
        <div class=\"progress\">
          <div class=\"progress-bar progress-bar-success {{ !params.finished ? \'progress-bar-striped active\' : \'\' }}\"
                  role=\"progressbar\" aria-valuenow=\"{{params.progress}}\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: {{params.progress}}%\">
            <span class=\"sr-only\">{{params.progress}}% Complete</span>
          </div>
        </div>
        <div ng-bind-html=\"params.msg\"></div>
    </div>
</div>',
            ),
        );
    }

}