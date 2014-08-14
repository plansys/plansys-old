<?php

class AdminUser extends User {
    public function getFields() {
        return array (
            array (
                'label' => 'Text Field',
                'name' => 'bug fix',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Field',
                'name' => 'username',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Field',
                'name' => 'tew',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Field',
                'name' => 'qwdq',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Field',
                'name' => 'dqw',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Fielddwq',
                'name' => 'fe',
                'type' => 'TextField',
            ),
        );
    }
    public function getForm() {
        return array (
            'formTitle' => 'User',
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

}
