<?php

class DevEditScript extends Form {

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '
<a ng-href=\"{{ Yii.app.createUrl(\'/dev/forms/update&class=\' + params.name ) }}\"
   class=\"btn btn-default btn-xs pull-right\" style=\"margin: 4px -10px 4px 2px;
    font-size: 11px;
    font-weight: bold;\">
    <i class=\"fa fa-pencil-square-o\"></i> Edit Form
</a>
<tabset class=\'tab-set single-tab\'>
    <tab>
        <tab-heading>
            {{params.shortname}} &bull; {{params.status}} 
        </tab-heading>
        <div style=\'padding:0px 0px;width:100%;\'>
            <div class=\"text-editor-builder\">
              <div class=\"text-editor\" ui-ace=\"aceConfig({
              mode: params.mode
              })\" style=\"position:absolute;top:28px;font-size:14px;left:0px;right:0px;bottom:0px\" ng-model=\"params.content\">
                </div>
            </div>
        </div>
    </tab>
</tabset>
',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Edit Script',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'DevEditScript.js',
        );
    }

}