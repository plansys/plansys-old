<?php

class DevUserForm extends User {
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Cancel',
                        'url' => '/dev/user/index',
                        'type' => 'LinkButton',
                    ),
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '{{!isNewRecord ? \\\'User #\\\' + model.username : \\\'New User\\\'}}',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Username',
                        'name' => 'username',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Fullname',
                        'name' => 'fullname',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'User Role',
                        'name' => 'roles',
                        'fieldTemplate' => 'form',
                        'type' => 'ListView',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'column2' => array (
                    array (
                        'label' => 'NIP',
                        'name' => 'nip',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Phone',
                        'name' => 'phone',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Email',
                        'name' => 'email',
                        'labelWidth' => '2',
                        'type' => 'TextField',
                    ),
                    '<column-placeholder></column-placeholder>',
                ),
                'type' => 'ColumnField',
            ),
        );
    }
    public function getForm() {
        return array (
            'title' => 'UserForm',
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
