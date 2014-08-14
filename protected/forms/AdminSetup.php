<?php

class AdminSetup extends Form {
    public function getFields() {
        return array (
            '<h2><center>{{ form.formTitle }}</center></h2><hr/>',
            array (
                'label' => 'Server',
                'name' => 'server',
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
                'type' => 'TextField',
            ),
            array (
                'label' => 'DB name',
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
            'formTitle' => 'Setup',
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
    public $username;
    public $password;
    public $dbname;
  
}
