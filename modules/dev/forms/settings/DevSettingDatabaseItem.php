<?php

class DevSettingDatabaseItem extends DevSettingDatabase {
    public $conn = '';
    
    public function rules() {
        return [
            ["conn", "required"]
        ];
    }

    public function getForm() {
        return array (
            'title' => 'Setting Database Item',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => '',
        );
    }

    public function getFields() {
        return array (
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Connection Name',
                        'name' => 'conn',
                        'prefix' => 'db',
                        'options' => array (
                            'ng-change' => 'model.conn = formatName(model.conn)',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<hr>',
                    ),
                    array (
                        'label' => 'Driver',
                        'name' => 'driver',
                        'list' => array (
                            'mysql' => 'MySQL',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Host',
                        'name' => 'host',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Port',
                        'name' => 'port',
                        'fieldWidth' => '3',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
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
                        'label' => 'Database',
                        'name' => 'dbname',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'perColumnOptions' => array (
                    'style' => 'margin:0px; padding:5px;',
                ),
                'type' => 'ColumnField',
            ),
        );
    }

}