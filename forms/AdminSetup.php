<?php

class AdminSetup extends Form {
    public function getFields() {
        return array (
            '<h2><center>{{ form.title }}</center></h2><hr/>',
            array (
                'label' => 'Driver',
                'name' => 'driver',
                'list' => array (
                    'mysql' => 'MySQL',
                ),
                'listExpr' => 'Setting::getDBDriverList();',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'Server',
                'name' => 'server',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Port',
                'name' => 'port',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Username',
                'name' => 'username',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password',
                'name' => 'password',
                'fieldType' => 'password',
                'type' => 'TextField',
            ),
            array (
                'label' => 'DB Name',
                'name' => 'dbname',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Submit',
                'type' => 'SubmitButton',
            ),
        );
    }
    public function getForm() {
        return array (
            'title' => 'AdminSetup',
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
    
    public $driver;
    public $server;
    public $port;
    public $username;
    public $password;
    public $dbname;
  
}
