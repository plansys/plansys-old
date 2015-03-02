<?php

class DevSettings extends Form {
    #Application's Setting
    public $appName;
    public $appMode;
    public $appDir;
    public $appHost;
    public $appPassEncrypt;
    
    #DB's Settings
    public $dbSys;
    public $dbServer;
    public $dbUser;
    public $dbPass;
    public $dbName;
    public $dbPort;
    
    #Email's Settings
    public $emailService;
    public $emailSender;
    
    #LDAP's Settings
    public $ldapEnable;
    public $ldapAdPort;
    public $ldapDomainControllers;
    public $ldapAccountSuffix;
    public $ldapBaseDn;
    public $ldapUsename;
    public $ldapPassword;
    
    #Notification's Settings
    public $notifEnable;
    public $notifWithEmail;
    
    #Repo's Settings
    public $repoPath;
    
    #Audit Trail's Settings
    public $auditEnable;
    public $auditTrack;
    
    public function getForm() {
        return array (
            'title' => 'Settings',
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

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save',
                        'buttonType' => 'success',
                        'options' => array (
                            'ng-click' => 'form.submit(this)',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'title' => 'Plansys Settings',
                'firstTabName' => 'Application Settings',
                'type' => 'ActionBar',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Application Name',
                        'name' => 'appName',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Application Directory',
                        'name' => 'appDir',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Mode',
                        'name' => 'appMode',
                        'list' => array (
                            'development' => 'Development',
                            'production' => 'Production',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Password Encryption',
                        'name' => 'appPassEncrypt',
                        'list' => array (
                            'md5' => 'md5',
                            'bcrypt' => 'bcrypt',
                            'php_password' => 'PHP Password',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Database Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Test Database',
                'icon' => 'link',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 0px 0px 0px;',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'System',
                        'name' => 'dbSys',
                        'listExpr' => '[\\\'mysql\\\' => \\\'MySQL\\\']',
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Database',
                        'name' => 'dbName',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Port',
                        'name' => 'dbPort',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Server',
                        'name' => 'dbServer',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'dbUser',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'dbPass',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Repository & Notification',
                'type' => 'SectionHeader',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Repo Path',
                        'name' => 'repoPath',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Test Repo',
                        'icon' => 'link',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;margin:0px 0px 0px 0px;',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Enable Notification',
                        'name' => 'notifEnable',
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'label' => 'Enable Email Notification',
                        'name' => 'notifWithEmail',
                        'options' => array (
                            'ng-if' => '!!model.notifEnable',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'value' => '<div class=\\"col-sm-4\\"></div>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Test Notification',
                        'icon' => 'link',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'ng-if' => '!!model.notifEnable',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Audit Trails',
                'type' => 'SectionHeader',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Enable Audit Trail',
                        'name' => 'auditEnable',
                        'options' => array (
                            'ng-change' => 'changeEnableAudit()',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'label' => 'Track Operation(s)',
                        'name' => 'auditTrack',
                        'list' => array (
                            'view' => 'View',
                            'save' => 'Create/Update',
                            'delete' => 'Delete',
                            'login' => 'Login/Logout',
                        ),
                        'options' => array (
                            'ng-if' => '!!model.auditEnable',
                        ),
                        'type' => 'CheckboxList',
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
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Email Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Test Email',
                'icon' => 'link',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 0px 0px 0px;',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Service',
                        'name' => 'emailService',
                        'defaultType' => 'first',
                        'list' => array (
                            'gmail' => 'GMail',
                            'ses' => 'Amazon SES',
                            'smtp' => 'SMTP',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Sender',
                        'name' => 'emailSender',
                        'type' => 'TextField',
                    ),
                    array (
                        'value' => '<div class=\"col-sm-6\" 
     style=\"float:right;margin:-5px 0px 0px 0px;padding:0px;text-align:right;color:#999;font-size:12px;width:60%\">
      <i class=\"fa fa-info-circle\"></i> 
    Sender Name < sender@server.com >
</div>',
                        'type' => 'Text',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'name' => 'subForm1',
                        'subForm' => 'application.modules.dev.forms.settings.DevSettingsEmailSmtp',
                        'options' => array (
                            'ng-if' => 'model.emailService == \\\'smtp\\\'',
                        ),
                        'type' => 'SubForm',
                    ),
                    array (
                        'name' => 'subForm2',
                        'subForm' => 'application.modules.dev.forms.settings.DevSettingsEmailGmail',
                        'options' => array (
                            'ng-if' => 'model.emailService == \\\'gmail\\\'',
                        ),
                        'type' => 'SubForm',
                    ),
                    array (
                        'name' => 'subForm3',
                        'subForm' => 'application.modules.dev.forms.settings.DevSettingsEmailSes',
                        'options' => array (
                            'ng-if' => 'model.emailService == \\\'ses\\\'',
                        ),
                        'type' => 'SubForm',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'LDAP Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'label' => 'Test LDAP',
                'icon' => 'link',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 0px 0px 0px;',
                    'ng-if' => '!!model.ldapEnable',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'column1' => array (
                    array (
                        'label' => 'Enable LDAP',
                        'name' => 'ldapEnable',
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'type' => 'ColumnField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'AD Port',
                        'name' => 'ldapAdPort',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Account Suffix',
                        'name' => 'ldapAccountSuffix',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Base DN',
                        'name' => 'ldapBaseDn',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'ldapUsename',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'ldapPassword',
                        'type' => 'TextField',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Domain Controllers',
                        'name' => 'ldapDomainControllers',
                        'type' => 'ListView',
                    ),
                ),
                'options' => array (
                    'style' => 'margin-top : -45px;',
                    'ng-if' => '!!model.ldapEnable',
                ),
                'type' => 'ColumnField',
            ),
        );
    }

}