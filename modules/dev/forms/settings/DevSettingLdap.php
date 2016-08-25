<?php

class DevSettingLdap extends Form {

    public $enable = 'NO';
    public $domain_controllers = [''];
    public $account_suffix = '';
    public $base_dn = '';
    public $admin_username = '';
    public $admin_password = '';
    public $ad_port = '389';
    public $use_ssl = 'NO';
    public $use_tls = 'NO';
    
    public function __construct() {
        parent::__construct();
        
        $setting = Setting::get('ldap');
        
        
        if (isset($setting['account_suffix'])) {
            $setting['account_suffix'] = str_replace("@", "", $setting['account_suffix']);
        }
        
        $setting['use_tls'] = @$setting['use_tls'] ? 'YES' : 'NO';
        $setting['use_ssl'] = @$setting['use_ssl'] ? 'YES' : 'NO';
        
        $this->attributes = $setting;
        $this->enable = @$setting['enable'] ? 'YES' : 'NO';
        
    }
    
    public function save() {
        $setting = $this->attributes;
        $setting['enable'] = $this->enable == 'YES';
        
        $dc = [];
        foreach ($setting['domain_controllers'] as $k) {
            if (isset($k['val'])) {
                $dc[] = $k['val'];
            }
        }
        $setting['account_suffix'] = '@' . $setting['account_suffix'];
        $setting['domain_controllers'] = $dc;
        $this->domain_controllers = $dc;
        
        if ($setting['account_suffix'] == '@') {
            $setting['account_suffix'] = "";
        }
        
        if ($setting['admin_username'] == '') {
            unset($setting['admin_username']);
        }
        
        $setting['use_tls'] = $setting['use_tls'] == 'YES';
        $setting['use_ssl'] = $setting['use_ssl'] == 'YES';
        
        if ($setting['admin_password'] == '') {
            unset($setting['admin_password']);
        }
        
        Setting::set('ldap', $setting);
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
                'title' => '<i class=\\"fa fa-users\\"></i> {{form.title}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Use LDAP',
                        'name' => 'enable',
                        'onLabel' => 'YES',
                        'offLabel' => 'NO',
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'name' => 'domain_controllers',
                        'fieldTemplate' => 'default',
                        'label' => 'Domain Controller',
                        'labelWidth' => '4',
                        'minItem' => 1,
                        'fieldWidth' => '8',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
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
                    array (
                        'label' => 'AD Port',
                        'name' => 'ad_port',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'fieldOptions' => array (
                            'placeholder' => '389',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<hr>',
                    ),
                    array (
                        'label' => 'Use LDAP through SSL (LDAPS)',
                        'name' => 'use_ssl',
                        'onLabel' => 'YES',
                        'offLabel' => 'NO',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'label' => 'Use LDAP through TLS',
                        'name' => 'use_tls',
                        'onLabel' => 'YES',
                        'offLabel' => 'NO',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Account Suffix',
                        'name' => 'account_suffix',
                        'prefix' => '@',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Base DN',
                        'name' => 'base_dn',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'TextArea',
                    ),
                    array (
                        'label' => 'Bind (R)DN<br/> <small>or Username</small>',
                        'name' => 'admin_username',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'admin_password',
                        'fieldType' => 'password',
                        'options' => array (
                            'ng-if' => 'model.enable == \'YES\'',
                        ),
                        'type' => 'TextField',
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
            'title' => 'LDAP Setting',
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