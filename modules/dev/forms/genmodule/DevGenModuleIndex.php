<?php

class DevGenModuleIndex extends Form {

    public function getForm() {
        return array (
            'title' => 'Daftar Gen Module ',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.GenModule',
                        'title' => 'Manteb',
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                        'name' => 'col2',
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