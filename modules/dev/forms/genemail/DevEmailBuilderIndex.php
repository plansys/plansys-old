<?php

class DevEmailBuilderIndex extends Form {

    public function getForm() {
        return array (
            'title' => 'Email Builder',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenEmail',
                        'inlineJS' => 'GenEmail.js',
                        'title' => 'Email Template',
                        'icon' => 'fa-envelope-o',
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
            'inlineJS' => 'DevEmailBuilderIndex.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<a style=\"font-size:11px;font-weight:bold;margin:4px -11px 0px 0px;\" ng-url=\'/dev/genEmail/preview&template={{params.active}}\' ng-if=\'params.active\'
    target=\"_blank\"
   class=\"btn btn-xs btn-success pull-right\">
    <i class=\"fa fa-play\"></i> Preview 
</a>',
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
<tabset class=\'single-tab tab-set\' ng-if=\'!!params.active\'>
<tab active=\"true\">
    <tab-heading>
        <i class=\"fa fa-cube\"></i>
        {{params.name}} &bull; {{status}}
    </tab-heading>
    <div style=\'padding:0px 0px;\'>
        ',
            ),
            array (
                'type' => 'Text',
                'value' => ' <div class=\"text-editor-builder\">
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
</tabset>',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'edPopup',
                'parentForm' => 'application.modules.dev.forms.genemail.DevEmailBuilderIndex',
            ),
        );
    }

}