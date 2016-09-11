<?php

class DevSettingDatabase extends Form {

    public $driver = 'mysql';
    public $host = 'localhost';
    public $port = '3306';
    public $username = 'root';
    public $password = '';
    public $dbname = '';
    public $items = [];
    
    
    public function __construct() {
        parent::__construct();
        $this->attributes = Setting::get('db');
    }
    
    public function save() {
        if ($this->validate()) {
            Setting::set('db', $this->attributes);
            return true;
        } else {
            return false;
        }
    }
    
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save Setting',
                        'buttonType' => 'success',
                        'icon' => 'check-square',
                        'options' => array (
                            'confirm' => 'Are you sure want to change database setting ?',
                            'ng-click' => 'form.submit(this);',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '<i class=\\"fa fa-database\\"></i> {{form.title}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'type' => 'Text',
                'value' => '<div ng-if=\"!params.posted\" class=\"alert alert-warning text-center\">
    <i class=\"fa fa-warning\"></i> <b>WARNING:</b> Database errors will cause your application to stop working
</div>',
            ),
            array (
                'title' => 'Primary Database',
                'type' => 'SectionHeader',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Driver',
                        'name' => 'driver',
                        'listExpr' => 'Setting::getDBDriverList();',
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
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Optional Database',
                'type' => 'SectionHeader',
            ),
            array (
                'name' => 'items',
                'fieldTemplate' => 'form',
                'templateForm' => 'application.modules.dev.forms.settings.DevSettingDatabaseItem',
                'inlineJS' => 'DevSettingDatabaseItem.js',
                'singleViewOption' => array (
                    'name' => 'val',
                    'fieldType' => 'text',
                    'labelWidth' => 0,
                    'fieldWidth' => 12,
                    'fieldOptions' => array (
                        'ng-delay' => 500,
                    ),
                ),
                'type' => 'ListView',
            ),
        );
    }

    public function getForm() {
        return array (
            'title' => 'Database Setting',
            'layout' => array (
                'name' => '2-cols',
                'data' => array (
                    'col1' => array (
                        'size' => '200',
                        'sizetype' => 'px',
                        'type' => 'menu',
                        'name' => 'col1',
                        'file' => 'application.modules.dev.menus.Setting',
                        'icon' => 'fa-sliders',
                        'title' => 'Main Setting',
                        'menuOptions' => array (),
                    ),
                    'col2' => array (
                        'size' => '',
                        'sizetype' => '',
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

}