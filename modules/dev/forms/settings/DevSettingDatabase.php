<?php

class DevSettingDatabase extends Form {

    public $driver = 'mysql';
    public $host = 'localhost';
    public $username = 'root';
    public $password = '';
    public $dbname = '';
    
    public function __construct() {
        parent::__construct();
        
        $this->attributes = Setting::get('db');
    }
    
    public function save() {
        Setting::set('db', $this->attributes);
        return true;
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
                'value' => '<div class=\"alert alert-warning text-center\">
    <i class=\"fa fa-warning\"></i> <b>WARNING:</b> Database errors will cause your application to stop working
</div>',
            ),
            array (
                'type' => 'Text',
                'value' => '<hr style=\\"margin:0px -15px;\\">',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
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
                'type' => 'Text',
                'value' => '<hr style=\\"margin:0px -15px;\\">',
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