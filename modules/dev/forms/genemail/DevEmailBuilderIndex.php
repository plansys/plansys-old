<?php

class DevEmailBuilderIndex extends Form {

    public function getForm() {
        return array (
            'title' => 'Daftar Email Builder ',
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
            'inlineJS' => 'indexEmail.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'label' => 'Preview',
                'buttonType' => 'success',
                'icon' => 'save',
                'position' => 'right',
                'options' => array (
                    'href' => 'url:/dev/email/preview&template={params.template}',
                    'target' => '_blank',
                    'ng-click' => 'emailvaluePopUp.open()',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'type' => 'Text',
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
        {{params.name}} Model &bull; {{status}}
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
</tabset>',
            ),
            array (
                'type' => 'PopupWindow',
                'name' => 'emailvaluePopUp',
                'mode' => 'url',
                'url' => '/dev/email/emailvalue?apa=ls&auhsj&',
            ),
        );
    }

}