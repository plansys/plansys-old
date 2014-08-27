<?php

class DevSettings extends Form {
    public function getForm() {
        return array (
            'title' => 'Settings',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col2' => array (
                        'type' => 'form',
                        'name' => 'col2',
                        'size' => '',
                        'sizetype' => '',
                    ),
                    'col1' => array (
                        'type' => 'mainform',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.Settings',
                        'title' => 'Settings',
                        'sizetype' => '',
                        'size' => '100',
                    ),
                ),
            ),
            'controller' => '',
            'updateAction' => 'actionUpdate',
            'createAction' => 'actionCreate',
        );
    }
    public function getFields() {
        return array (
        );
    }
    
}
