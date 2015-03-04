<?php

class DevSettings extends Form {
    public $errors;
    #Application's Setting
    public $appName;
    public $appMode;
    public $appDir;
    public $appHost;
    public $appPassEncrypt;
    
    #DB's Settings
    public $dbSys;
    public $dbHost;
    public $dbUser;
    public $dbPass;
    public $dbName;
    
    #Email's Settings
    public $emailService;
    public $emailSender;
    
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
        #App
        $this->appName = Setting::get('app.name');
        $this->appMode = Setting::get('app.mode');
        $this->appPassEncrypt = Setting::get('app.passEncrypt');
        
        #Database
        $this->dbSys = Setting::get('db.driver');
        $this->dbName = Setting::get('db.dbname');
        $this->dbPass = Setting::get('db.password');
        $this->dbHost = Setting::get('db.host');
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
    
    public function setSettings(){
        $data = $this->attributes;
        #App
        Setting::set('app.name',$data['appName'],false);
        Setting::set('app.mode',$data['appMode'],false);        
        Setting::set('app.passEncrypt',$data['appPassEncrypt'],false);
        
        #Database
        Setting::set('db.driver',$data['dbSys'],false);
        Setting::set('db.dbname',$data['dbName'],false);
        Setting::set('db.password',$data['dbPass'],false);
        Setting::set('db.host',$data['dbHost'],false);
        Setting::set('db.username',$data['dbUser'],false);
        
        #Repo
        Setting::set('repo.path',$data['repoPath'],false);
        
        #Notif
        $enableNotif = true;
        $enableNotifEmail = true;
        if($data['notifEnable'] != 'on'){
            $enableNotif = false;
            $data['notifWithEmail'] = false;
        }
        if($data['notifWithEmail'] != 'on'){
            $enableNotifEmail = false;
        }
        Setting::set('notif.enable',$enableNotif,false); 
        Setting::set('notif.email',$enableNotifEmail,false);
        
        #Audit Trail 
        $enableAudit = true;
        if($data['auditEnable'] != 'on'){
            $enableAudit = false;
        }
        Setting::set("auditTrail.enable",$enableAudit,false);
        if($enableAudit){
            Setting::set("auditTrail.track",$data['auditTrack'],false);
        }else{
            Setting::set("auditTrail.track",null,false);
        }
        
        #Email
        Setting::remove("email");
        if($data['emailService'] == 'ses'){
            Setting::set("email.transport.auth.accessKeyId",$data['emailAccessKeyId'],false);
            Setting::set("email.transport.auth.secretAccessKey",$data['emailSecretAccessKey'],false);
            Setting::set("email.transport.auth.rateLimit",$data['emailRateLimit'],false);
            Setting::set("email.transport.auth.region",$data['emailRegion'],false);
        }elseif($data['emailService'] == 'gmail'){
            Setting::set("email.transport.auth.user",$data['emailUser'],false);
            Setting::set("email.transport.auth.pass",$data['emailPass'],false);
        }
        elseif($data['emailService'] == 'smtp'){
            Setting::set("email.transport.auth.user",$data['emailUser'],false);
            Setting::set("email.transport.auth.pass",$data['emailPass'],false);
            Setting::set("email.transport.host",$data['emailHost'],false);
            Setting::set("email.transport.port",$data['emailPort'],false);
        }
        
        if($data['emailService'] != 'none'){
            Setting::set("email.from",$data['emailSender'],false);
        }else{
            Setting::set("email.from",null,false);
        }
        
        if($data['emailService'] == 'smtp' || $data['emailService'] == 'none'){
            Setting::set("email.service",null,false);
        }else{
            Setting::set("email.service",$data['emailService'],false);
        }
        
        
        #LDAP
        if($data['ldapEnable'] == 'on'){
            Setting::set("ldap.enable",true,false);
            Setting::set("ldap.ad_port",$data['ldapAdPort'],false);
            Setting::set("ldap.account_suffix",$data['ldapAccountSuffix'],false);
            Setting::set("ldap.base_dn",$data['ldapBaseDn'],false);
            Setting::set("ldap.domain_controllers",$data['ldapDomainControllers'],false);
            Setting::set("ldap.admin_password",$data['ldapPassword'],false);
            Setting::set("ldap.admin_username",$data['ldapUsername'],false);
        }else{
            Setting::remove("ldap");
            Setting::set("ldap.enable",false,false);
        }
        
        Setting::write();
        return true;
    }
    
    public function getForm() {
        return array (
            'title' => 'Settings',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => '/js/form.js',
        );
    }

    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'Save Settings',
                        'buttonType' => 'success',
                        'icon' => 'check',
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
                'value' => '<div style=\\"margin:20px auto;width:900px;border:1px solid #ddd;border-radius:5px;padding:0px 15px;box-shadow:0px 0px 10px 0px #ddd;\\">',
                'type' => 'Text',
            ),
            array (
                'name' => 'errors',
                'type' => 'HiddenField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'App Name',
                        'name' => 'appName',
                        'type' => 'TextField',
                    ),
                    array (
                        'label' => 'App Mode',
                        'name' => 'appMode',
                        'list' => array (
                            'development' => 'Development',
                            'production' => 'Production',
                            '---' => '---',
                            'plansys' => 'Plansys Dev',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
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
                'renderInEditor' => 'Yes',
                'value' => '<i class=\"fa fa-warning fa-fw\" style=\"color:red;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'dbSys\']) != \'undefined\' && !loading.db\"></i>

<i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'dbSys\']) == \'undefined\' && !loading.db\"></i>


<i class=\"fa fa-spin fa-refresh\" style=\"float:right;margin:-21px -5px 0px 0px;\" ng-if=\"!!loading.db\"></i>',
                'type' => 'Text',
            ),
            array (
                'label' => 'Check Database',
                'icon' => 'database',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 25px 0px 0px;',
                    'ng-click' => 'checkDb()',
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
                        'value' => '<column-placeholder></column-placeholder>',
                        'type' => 'Text',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Host',
                        'name' => 'dbHost',
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '<div ng-if = \"model.emailService != \'none\'\" class=\"col-sm-12\" 
     style=\"margin:-5px 0px 8px 0px;padding:0px;text-align:right;color:#999;font-size:12px\">
    e.g. localhost:3306
</div>',
                        'type' => 'Text',
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
                        'label' => 'Check Repository',
                        'icon' => 'folder-open',
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
                        'labelWidth' => '6',
                        'fieldWidth' => '6',
                        'options' => array (
                            'ng-change' => 'changeEnableNotif()',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'label' => 'Enable Email Notification',
                        'name' => 'notifWithEmail',
                        'labelWidth' => '6',
                        'fieldWidth' => '6',
                        'options' => array (
                            'ng-if' => '!!model.notifEnable',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'value' => '<div class=\\"col-sm-6\\"></div>',
                        'type' => 'Text',
                    ),
                    array (
                        'label' => 'Test Notification',
                        'icon' => 'newspaper-o',
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
                'label' => 'Send Test Email',
                'icon' => 'envelope',
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
                            '---' => '---',
                            'none' => 'NONE',
                        ),
                        'type' => 'DropDownList',
                    ),
                    array (
                        'label' => 'Sender',
                        'name' => 'emailSender',
                        'options' => array (
                            'ng-if' => 'model.emailService != \\\'none\\\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'value' => '<div ng-if = \"model.emailService != \'none\'\" class=\"col-sm-6\" 
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
                'label' => 'Check LDAP',
                'icon' => 'user',
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
            array (
                'value' => '</div>',
                'type' => 'Text',
            ),
        );
    }

}