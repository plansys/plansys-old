<?php

class DevSettingApp extends Form {

    public $name = '';
    public $dir = 'app';
    public $host = 'http://localhost';
    public $mode = 'dev';
    public $debug = 'ON';
    public $dateFormat = 'd M Y';
    public $timeFormat = 'H:i';
    public $dateTimeFormat = 'd M Y - H:i';
    public $theme;
    public $auditTrail = 'Enabled';
    
    public $phpPath = '';
    
    public function __construct() {
        parent::__construct();
        $this->attributes = Setting::get('app');
    }
    
    public function save() {
        if ($this->attributes['mode'] == 'prod') {
            $va = $this->attributes;
            $va['debug'] = 'OFF';
            $this->attributes = $va;
        }
         
        Setting::set('app', $this->attributes);
        return true;
    }

    public function getForm() {
        return array (
            'title' => 'Application Setting',
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
            'inlineJS' => 'DevSettingApp.js',
        );
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
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => '<i class=\\"fa fa-home\\"></i> {{form.title}}',
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Application Name',
                        'name' => 'name',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Main Dir',
                        'name' => 'dir',
                        'fieldOptions' => array (
                            'disabled' => 'true',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Mode',
                        'name' => 'mode',
                        'list' => array (
                            'dev' => 'Development',
                            'prod' => 'Production',
                            '---' => '---',
                            'plansys' => 'Plansys Development',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Debug',
                        'name' => 'debug',
                        'options' => array (
                            'ng-if' => 'model.mode != \'prod\'',
                        ),
                        'type' => 'ToggleSwitch',
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
                'title' => 'Locale Setting',
                'type' => 'SectionHeader',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Date Format',
                        'name' => 'dateFormat',
                        'postfix' => '{{ timestamp | dateFormat:model.dateFormat }}',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Time Format',
                        'name' => 'timeFormat',
                        'postfix' => '{{ timestamp | dateFormat:model.timeFormat }}',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Date Time Format',
                        'name' => 'dateTimeFormat',
                        'postfix' => '{{ timestamp | dateFormat:model.dateTimeFormat }}',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Audit Trail',
                        'name' => 'auditTrail',
                        'onLabel' => 'Enabled',
                        'offLabel' => 'Disabled',
                        'type' => 'ToggleSwitch',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'System Setting',
                'type' => 'SectionHeader',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'PHP CLI Path',
                        'name' => 'phpPath',
                        'fieldOptions' => array (
                            'placeholder' => 'ex: /usr/bin/php -or- c:\\\\xampp\\\\php\\\\php.exe',
                        ),
                        'type' => 'TextArea',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<div class=\"col-md-4\"></div>
<div class=\"col-md-8\">
    <small>
        <i class=\"fa fa-info-circle \"></i>
        if not filled, plansys will search php binary in the environment variable
    </small>
</div>',
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

}