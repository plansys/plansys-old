<?php

class DevSettingsEmailGmail extends Form{
    public $emailUser;
    public $emailPass;
    public function getForm() {
        return array (
            'title' => 'Settings Email Gmail',
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
                'label' => 'Username',
                'name' => 'emailUser',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password',
                'name' => 'emailPass',
                'fieldType' => 'password',
                'type' => 'TextField',
            ),
        );
    }

}