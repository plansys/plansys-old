<?php

class DevSettingsEmailSmtp extends Form{
    public $emailUser;
    public $emailPass;
    public $emailHost;
    public $emailPort;
    public function getForm() {
        return array (
            'title' => 'Settings Email Smtp',
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
            array (
                'label' => 'Host',
                'name' => 'emailHost',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Port',
                'name' => 'emailPort',
                'type' => 'TextField',
            ),
        );
    }

}