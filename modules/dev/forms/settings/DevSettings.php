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
        if(Setting::get('notif.enable') == true){
            $this->notifEnable = "ON";
        }else{
            $this->notifEnable = "OFF";
        }
        
        if(Setting::get('notif.enable') == true && Setting::get('notif.email') == true){
            $this->notifWithEmail = "ON";
        }else{
            $this->notifWithEmail = "OFF";
        }
        
        #Audit Trail 
        if(Setting::get("auditTrail.enable")== true){
            $this->auditEnable = "ON";
        }else{
            $this->auditEnable = "OFF";
        }
        
        if($this->auditEnable == "ON"){
            $this->auditTrack = Setting::get("auditTrail.track");
        }else{
            $this->auditTrack = null;
        }
        
        #Email
        Email::initalSetting(true,true);
        $this->emailService = Setting::get("email.transport.service");
        if($this->emailService != 'none'){
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
        }
        
        #LDAP
        if(Setting::get("ldap.enable") == true){
            $this->ldapEnable = "ON";
        }else{
            $this->ldapEnable = "OFF";
        }
        
        if($this->ldapEnable == "ON"){
            $this->ldapAdPort = Setting::get("ldap.ad_port");
            $this->ldapAccountSuffix = Setting::get("ldap.account_suffix");
            $this->ldapBaseDn = Setting::get("ldap.base_dn");
            $this->ldapDomainControllers = Setting::get("ldap.domain_controllers");
            $this->ldapPassword = Setting::get("ldap.admin_password");
            $this->ldapUsername = Setting::get("ldap.admin_username");
        }
    }
    
    public function setSettings($data){
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
        if($data['notifEnable'] != 'ON'){
            $enableNotif = false;
            $data['notifWithEmail'] = false;
        }
        if($data['notifWithEmail'] != 'ON'){
            $enableNotifEmail = false;
        }
        Setting::set('notif.enable',$enableNotif,false); 
        Setting::set('notif.email',$enableNotifEmail,false);
        
        #Audit Trail 
        $enableAudit = true;
        if($data['auditEnable'] != 'ON'){
            $enableAudit = false;
        }
        Setting::set("auditTrail.enable",$enableAudit,false);
        if($enableAudit){
            Setting::set("auditTrail.track",$data['auditTrack'],false);
        }else{
            Setting::set("auditTrail.track",null,false);
        }
        
        #Email
        Setting::remove("email.transport");
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
        
        if($data['emailService'] == 'smtp'){
            Setting::set("email.transport.service",null,false);
        }elseif($data['emailService'] == 'none'){
            Setting::set("email.transport.service",'none',false);
        }else{
            Setting::set("email.transport.service",$data['emailService'],false);
        }
        
        
        #LDAP
        if($data['ldapEnable'] === 'ON'){
            Setting::set("ldap.enable",true,false);
            Setting::set("ldap.ad_port",$data['ldapAdPort'],false);
            Setting::set("ldap.account_suffix",$data['ldapAccountSuffix'],false);
            Setting::set("ldap.base_dn",$data['ldapBaseDn'],false);
            Setting::set("ldap.domain_controllers",$data['ldapDomainControllers'],false);
            Setting::set("ldap.admin_password",$data['ldapPassword'],false);
            Setting::set("ldap.admin_username",$data['ldapUsername'],false);
        }else{
            Setting::remove("ldap.ad_port");
            Setting::remove("ldap.account_suffix");
            Setting::remove("ldap.base_dn");
            Setting::remove("ldap.domain_controllers");
            Setting::remove("ldap.admin_password");
            Setting::remove("ldap.admin_username");
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
                'type' => 'Text',
                'value' => '<div style=\\"margin:20px auto;width:900px;border:1px solid #ddd;border-radius:5px;padding:0px 15px;box-shadow:0px 0px 10px 0px #ddd;\\">',
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
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div ng-if = \"model.emailService != \'none\'\" class=\"col-sm-6\" 
     style=\"float:right;margin:-5px 0px 0px 0px;padding:0px;text-align:right;color:#999;font-size:12px;width:65%\">
      <i class=\"fa fa-info-circle\"></i> 
    Changing App Name, will make you logged out automatically
</div><br>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'Database Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<i class=\"fa fa-warning fa-fw\" style=\"color:red;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'dbSys\']) != \'undefined\' && !loading.db\"></i>

<i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'dbSys\']) == \'undefined\' && !loading.db\"></i>


<i class=\"fa fa-spin fa-refresh\" style=\"float:right;margin:-21px -5px 0px 0px;\" ng-if=\"!!loading.db\"></i>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
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
                        'type' => 'Text',
                        'value' => '<div ng-if = \"model.emailService != \'none\'\" class=\"col-sm-12\" 
     style=\"margin:-5px 0px 8px 0px;padding:0px;text-align:right;color:#999;font-size:12px\">
    e.g. localhost:3306
</div>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'title' => 'Repository',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'label' => 'Check Repository',
                        'icon' => 'folder-open',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'style' => 'float:right;margin:-50px -15px 0px 0px;',
                            'ng-click' => 'checkRepo()',
                        ),
                        'type' => 'LinkButton',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div style=\"float:right;margin:-50px -40px 0px 0px;\">
    <i class=\"fa fa-warning fa-fw\" style=\"color:red;\"
    ng-if=\"typeof(errors[\'repoPath\']) != \'undefined\' && !loading.repo\"></i>
    
    <i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;\" ng-if=\"typeof(errors[\'repoPath\']) == \'undefined\' && !loading.repo\"></i>
    
    <i class=\"fa fa-spin fa-refresh\" ng-if=\"!!loading.repo\"></i>
</div>',
                    ),
                    array (
                        'label' => 'Repo Path',
                        'name' => 'repoPath',
                        'type' => 'TextField',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'title' => 'Notification',
                        'type' => 'SectionHeader',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div style=\"float:right;margin:-50px -55px 0px 0px;\">
<i class=\"fa fa-warning fa-fw\" style=\"color:red;\" ng-if=\"typeof(errors[\'notifEnable\']) != \'undefined\' && !loading.notif  && model.notifEnable==\'ON\'\"></i>

<i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;\" ng-if=\"typeof(errors[\'notifEnable\']) == \'undefined\' &&  model.notifEnable == \'ON\' && !loading.notif  && model.notifEnable==\'ON\'\"></i>


<i class=\"fa fa-spin fa-refresh\" ng-if=\"!!loading.notif  && model.notifEnable==\'ON\'\"></i>
</div>',
                    ),
                    array (
                        'label' => 'Test Notification',
                        'icon' => 'newspaper-o',
                        'buttonSize' => 'btn-xs',
                        'options' => array (
                            'ng-if' => 'model.notifEnable == \\\'ON\\\'',
                            'ng-click' => 'checkNotif()',
                            'style' => 'float:right;margin:-50px -25px 0px 0px;',
                        ),
                        'type' => 'LinkButton',
                    ),
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
                            'ng-if' => 'model.notifEnable==\\\'ON\\\'',
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
                'type' => 'Text',
                'value' => '<div ng-if=\\"false\\">',
            ),
            array (
                'title' => 'Audit Trails',
                'type' => 'SectionHeader',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'label' => 'Enable Audit Trail',
                        'name' => 'auditEnable',
                        'labelWidth' => '5',
                        'fieldWidth' => '7',
                        'options' => array (
                            'ng-change' => 'changeEnableAudit()',
                        ),
                        'type' => 'ToggleSwitch',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'column2' => array (
                    array (
                        'label' => 'Track Operation(s)',
                        'name' => 'auditTrack',
                        'list' => array (
                            'view' => 'View',
                            'save' => 'Create/Update',
                            'delete' => 'Delete',
                            'login' => 'Login/Logout',
                        ),
                        'labelWidth' => '5',
                        'options' => array (
                            'ng-if' => 'model.auditEnable== \\\'ON\\\'',
                        ),
                        'type' => 'CheckboxList',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'w3' => '33%',
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
            array (
                'title' => 'Email Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<i class=\"fa fa-warning fa-fw\" style=\"color:red;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'emailService\']) != \'undefined\' && !loading.email  && model.emailService!=\'none\'\"></i>

<i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'emailService\']) == \'undefined\' && !loading.email  && model.emailService!=\'none\'\"></i>


<i class=\"fa fa-spin fa-refresh\" style=\"float:right;margin:-21px -5px 0px 0px;\" ng-if=\"!!loading.email  && model.emailService!=\'none\'\"></i>',
            ),
            array (
                'label' => 'Send Test Email',
                'icon' => 'envelope',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 25px 0px 0px;',
                    'ng-if' => 'model.emailService!=\\\'none\\\'',
                    'ng-click' => 'sendEmail()',
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
                        'label' => 'Sender E-mail',
                        'name' => 'emailSender',
                        'options' => array (
                            'ng-if' => 'model.emailService != \\\'none\\\'',
                        ),
                        'type' => 'TextField',
                    ),
                    array (
                        'renderInEditor' => 'Yes',
                        'type' => 'Text',
                        'value' => '<div ng-if = \"model.emailService != \'none\'\" class=\"col-sm-6\" 
     style=\"float:right;margin:-5px 0px 0px 0px;padding:0px;text-align:right;color:#999;font-size:12px;width:65%\">
      <i class=\"fa fa-info-circle\"></i> 
    Sender Email &lt;sender@server.com&gt;
</div>',
                    ),
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'title' => 'LDAP Settings',
                'type' => 'SectionHeader',
            ),
            array (
                'renderInEditor' => 'Yes',
                'type' => 'Text',
                'value' => '<i class=\"fa fa-warning fa-fw\" style=\"color:red;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'ldapEnable\']) != \'undefined\' && !loading.ldap && model.ldapEnable==\'ON\'\"></i>

<i class=\"fa fa-check fa-fw\" style=\"color:#67C03D;float:right;margin:-21px -5px 0px 0px;\" ng-if=\"typeof(errors[\'ldapEnable\']) == \'undefined\' && !loading.ldap && model.ldapEnable==\'ON\'\"></i>


<i class=\"fa fa-spin fa-refresh\" style=\"float:right;margin:-21px -5px 0px 0px;\" ng-if=\"!!loading.ldap && model.ldapEnable==\'ON\'\"></i>',
            ),
            array (
                'label' => 'Check LDAP',
                'icon' => 'user',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'float:right;margin:-25px 25px 0px 0px;',
                    'ng-if' => 'model.ldapEnable==\\\'ON\\\'',
                    'ng-click' => 'checkLdap()',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                ),
                'w1' => '50%',
                'w2' => '50%',
                'type' => 'ColumnField',
            ),
            array (
                'showBorder' => 'Yes',
                'column1' => array (
                    array (
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
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
                        'type' => 'Text',
                        'value' => '<column-placeholder></column-placeholder>',
                    ),
                    array (
                        'label' => 'Domain Controllers',
                        'name' => 'ldapDomainControllers',
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
                ),
                'w1' => '50%',
                'w2' => '50%',
                'options' => array (
                    'style' => 'margin-top : -45px;',
                    'ng-if' => 'model.ldapEnable==\\\'ON\\\'',
                ),
                'type' => 'ColumnField',
            ),
            array (
                'type' => 'Text',
                'value' => '</div>',
            ),
        );
    }

}