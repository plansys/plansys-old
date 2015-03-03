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
    public $emailUser;
    public $emailPass;
    public $emailHost;
    public $emailPort;
    public $emailAccessKeyId;
    public $emailSecretAccessKey;
    public $emailSessionToken;
    public $emailRegion;
    public $emailRateLimit;
    
    #LDAP's Settings
    public $ldapEnable;
    public $ldapAdPort;
    public $ldapDomainControllers;
    public $ldapAccountSuffix;
    public $ldapBaseDn;
    public $ldapUsername;
    public $ldapPassword;
    
    #Notification's Settings
    public $notifEnable;
    public $notifWithEmail;
    
    #Repo's Settings
    public $repoPath;
    
    #Audit Trail's Settings
    public $auditEnable;
    public $auditTrack;
    
    public function loadSettings(){
        
        //include Yii::getPathOfAlias('application.modules.dev.forms.settings.DevSettingsEmailSmtp').".php";
        #App
        $this->appName = Setting::get('app.name');
        $this->appDir = Setting::get('app.dir');
        $this->appMode = Setting::get('app.mode');
        $this->appHost = Setting::get('app.host');
        $this->appPassEncrypt = Setting::get('app.passEncrypt');
        
        #Database
        $this->dbSys = Setting::get('db.driver');
        $this->dbName = Setting::get('db.dbname');
        $this->dbPass = Setting::get('db.password');
        $this->dbPort = Setting::get('db.port');
        $this->dbServer = Setting::get('db.server');
        $this->dbUser = Setting::get('db.username');
        
        #Repo
        $this->repoPath = Setting::get('repo.path');
        
        #Notif
        $this->notifEnable = Setting::get('notif.enable');
        $this->notifWithEmail = Setting::get('notif.email');
        
        #Audit Trail 
        $this->auditEnable = Setting::get("auditTrail.enable");
        $this->auditTrack = Setting::get("auditTrail.track");
        
        #Email
        $this->emailService = Setting::get("email.service");
        if(is_null($this->emailService) || $this->emailService == ''){
            $this->emailService = 'smtp';
        }
        
        if($this->emailService == 'ses'){
            $this->emailAccessKeyId = Setting::get("email.transport.auth.accessKeyId");
            $this->emailSecretAccessKey = Setting::get("email.transport.auth.secretAccessKey");
            $this->emailRateLimit = Setting::get("email.transport.auth.rateLimit");
            $this->emailRegion = Setting::get("email.transport.auth.region");
        }else{
            $this->emailUser = Setting::get("email.transport.auth.user");
            $this->emailPass = Setting::get("email.transport.auth.pass");
        }
        
        if($this->emailService == 'smtp'){
            $this->emailHost = Setting::get("email.transport.host");
            $this->emailPort = Setting::get("email.transport.port");
        }
        
        $this->emailSender = Setting::get("email.from");
        
        #LDAP
        $this->ldapEnable = Setting::get("ldap.enable");
        $this->ldapAdPort = Setting::get("ldap.ad_port");
        $this->ldapAccountSuffix = Setting::get("ldap.account_suffix");
        $this->ldapBaseDn = Setting::get("ldap.base_dn");
        $this->ldapDomainControllers = Setting::get("ldap.domain_controllers");
        $this->ldapPassword = Setting::get("ldap.admin_password");
        $this->ldapUsername = Setting::get("ldap.admin_username");
    }
    
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
                        'label' => 'Application Host',
                        'name' => 'appHost',
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
                        'fieldType' => 'password',
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
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
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
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
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
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
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
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '<div ng-if = \\"model.emailService == \\\'smtp\\\' || model.emailService == \\\'gmail\\\'\\">',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Username',
                        'name' => 'emailUser',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'emailPass',
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '</div>
<div ng-if = \"model.emailService == \'smtp\'\">',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Host',
                        'name' => 'emailHost',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Port',
                        'name' => 'emailPort',
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '</div>
<div ng-if = \"model.emailService == \'ses\'\">',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Access Key ID',
                        'name' => 'emailAccessKeyId',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Secret Access Key',
                        'name' => 'emailSecretAccessKey',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Rate Limit',
                        'name' => 'emailRateLimit',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Region',
                        'name' => 'emailRegion',
                        'list' => array (
                            'us-east-1' => 'us-east-1',
                            'us-west-2' => 'us-west-2',
                            'eu-west-1' => 'eu-west-1',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '</div>',
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
                        'name' => 'ldapUsername',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'Password',
                        'name' => 'ldapPassword',
                        'fieldType' => 'password',
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