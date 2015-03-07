<?php

class DevGenModuleIndex extends Form {

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
                        'title' => 'Module',
                        'icon' => '',
                    ),
                    '' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenModule',
                        'title' => 'test',
                        'icon' => 'fa-empire',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
        );
    }

}