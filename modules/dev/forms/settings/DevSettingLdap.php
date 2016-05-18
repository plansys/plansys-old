<?php

class DevSettingLdap extends Form {

    public $enable = 'NO';
    public $domain_controllers = [''];
    public $account_suffix = '';
    public $base_dn = '';
    
    public function __construct() {
        parent::__construct();
        
        $setting = Setting::get('ldap');
        
        $this->attributes = $setting;
        $this->enable = @$setting['enable'] ? 'YES' : 'NO';
        
    }
    
    public function save() {
        $setting = $this->attributes;
        $setting['enable'] = $this->enable == 'YES';
        
        $dc = [];
        foreach ($setting['domain_controllers'] as $k) {
            $dc[] = $k['val'];
        }
        $setting['domain_controllers'] = $dc;
        $this->domain_controllers = $dc;
        
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
                        'type' => 'Text',
                        'value' => '<pre>{{ model | json }}</pre>',
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