<?php

class DevSettingEmail extends Form {
    public $from = '';
    public $transport = 'smtp';
    public $host = '';
    public $port = '25';
    public $username = '';
    public $password = '';
    
    public function __construct() {
        parent::__construct();
        
        $this->attributes = Setting::get('email');
    }
    
    public function save() {
        Setting::set('email', $this->attributes);
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
                            'ng-click' => 'form.submit(this);',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '<i class=\\"fa fa-envelope\\"></i> {{form.title}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Transport',
                        'name' => 'transport',
                        'list' => array (
                            'smtp' => 'SMTP',
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
                        'label' => 'From Address',
                        'name' => 'from',
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
        );
    }

    public function getForm() {
        return array (
            'title' => 'Email Setting',
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