<?php

class SysImportData extends Form {
    public $file;
    public $mode= 'update';

    public function getForm() {
        return array (
            'title' => 'Import Data',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div class=\"panel panel-default\" style=\"margin:20px auto;width:800px;\">
    <div class=\"panel-body\" style=\"padding:0px;\">
        <h3 style=\"
        margin:0px;
        padding:15px 15px 10px 15px;
        border-bottom:1px solid #ddd;
        text-align:center;
        \">Import Data: {{ params.m }}</h3>
        <div style=\"padding:10px 15px 15px 15px;\">
            ',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<div style=\"color:#555\">
    <b>Step 1: Download Template</b>
    <div class=\"text-center\" style=\"padding: 25px 0px 30px 0px;\">
        
        <a target=\"_blank\" ng-url=\"/sys/import/downloadTemplate&m={{params.m}}\" class=\"btn btn-success\">
            <i class=\"fa fa-download\"></i>         Download Excel Template
        </a>
        <br/>
        <br/>Download excel template and fill it with your data<br/>
        &mdash; Don\'t forget to remove example row &mdash;
        
    </div>
    <hr>
    <b>Step 2: Upload Excel Data</b>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<div style=\"
margin:20px auto;
width:300px;
text-align:center;\">
    <b style=\"margin-bottom:10px;display:block;\">Upload data:</b>',
            ),
            array (
                'name' => 'file',
                'label' => '',
                'mode' => 'Upload',
                'labelWidth' => '0',
                'fieldWidth' => '12',
                'allowDelete' => 'No',
                'type' => 'UploadFile',
            ),
            array (
                'type' => 'Text',
                'value' => '&mdash; Make sure you upload the correct file &mdash;
<br><br>
',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-click=\"form.submit(this)\" class=\"btn btn-primary btn-block\" ng-if=\"!!model.file\">
    <b>Execute Import Data</b>
        <i class=\"fa fa-chevron-right\"></i>
</div>
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '            </div>
        </div>
    </div>
</div>',
            ),
        );
    }

}