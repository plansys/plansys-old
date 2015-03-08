<?php

class DevGenModule extends Form {

    public function getForm() {
        return array (
            'title' => 'Generate Module',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenModule',
                        'title' => 'test',
                        'icon' => 'fa-empire',
                    ),
                    'col2' => array (
                        'type' => 'mainform',
                        'name' => 'col2',
                        'sizetype' => '%',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'value' => '<!-- MODULE INFO TAB -->
<tabset class=\'tab-set\'>
<tab heading=\"Module Info\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<!-- ACCESS CONTROL TAB -->
</tab><tab heading=\"Access Control\">',
                'type' => 'Text',
            ),
            array (
                'value' => '<!-- TAB CLOSER -->
</tab></tabset>',
                'type' => 'Text',
            ),
        );
    }

}