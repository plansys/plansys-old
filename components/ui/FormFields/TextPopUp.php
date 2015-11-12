<?php

class TextPopUp extends Form {
    
    public function getForm() {
        return array (
            'title' => 'Pop Up',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'Text/textPopUp.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'type' => 'Text',
                'value' => '<div ui-ace=\"aceConfig({
  mode: \'html\',
  onLoad: aceLoaded
})\" 
ng-change=\"save()\" ng-delay=\"500\"
style=\"
position:absolute;
width:100%;
height:100%;
top:0px;
left:0px;
right:0px;
left:0px;
\" ng-model=\"code\">
</div>',
            ),
        );
    }

}