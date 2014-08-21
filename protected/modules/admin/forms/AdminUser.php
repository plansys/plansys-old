<?php

class AdminUser extends User {
    public function getFields() {
        return array (
            array (
                'label' => 'few',
                'group' => 'test',
                'groupType' => 'DropDown',
                'buttonType' => 'success',
                'icon' => 'eye-slash',
                'buttonSize' => 'btn-sm',
                'type' => 'LinkButton',
            ),
            array (
                'label' => 'aduhhhhh',
                'group' => 'test',
                'groupType' => 'DropDown',
                'buttonType' => 'success',
                'icon' => 'eye-slash',
                'buttonSize' => 'btn-sm',
                'options' => array (
                    'ng-click' => 'form.submit()',
                ),
                'type' => 'LinkButton',
            ),
            '<h2><center>{{ form.formTitle }}</center></h2><hr/>',
            array (
                'name' => 'id',
                'type' => 'HiddenField',
            ),
            array (
                'label' => 'Lastname',
                'name' => 'lastname',
                'type' => 'TextField',
            ),
            '',
            '',
            array (
                'label' => 'Firstname',
                'name' => 'firstname',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Nip',
                'name' => 'nip',
                'options' => array (
                    'ng-model' => 'model.nip',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Phone',
                'name' => 'phone',
                'type' => 'TextField',
            ),
            '',
            array (
                'label' => 'Email',
                'name' => 'email',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Password',
                'name' => 'password',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Text Field',
                'name' => 'Text Field 8',
                'type' => 'TextField',
            ),
            array (
                'label' => 'Date',
                'name' => 'date',
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
    //put your code here
}
