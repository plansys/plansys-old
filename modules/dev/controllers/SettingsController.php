<?php

class SettingsController extends Controller {
    public function actionIndex() {
       $model = new DevSettings;
       $model->loadSettings();
       if(isset($_POST["DevSettings"])){
           if($_POST["DevSettings"]["emailService"] !== 'none'){
               $emailSub = "DevSettingsEmail".ucfirst($_POST["DevSettings"]["emailService"]); 
               $_POST['DevSettings'] = array_merge($_POST['DevSettings'], $_POST[$emailSub]);
           }
           $settings = $model->attributes;
           foreach($settings as $k=>$set){
               if(isset($_POST["DevSettings"][$k])){
                   $settings[$k] = $_POST["DevSettings"][$k];
               }
           }
           
           $model->setSettings($settings);
           Yii::app()->user->setFlash('info', 'Data Berhasil Disimpan');
       }
       $this->renderForm("DevSettings",$model);
    }
    
    public function actionDb() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        
        $error = null;
        if (!empty($post)) {
            try {
                $dbh = new pdo("{$post['sys']}:host={$post['host']};dbname={$post['dbname']}", $post['username'], $post['password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            } catch (PDOException $ex) {
                $error = $ex->getMessage();
            }
            
        }
        echo json_encode($error);
    }
    
    public function actionRepo(){
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        
        $error = null;
        if (!empty($post)) {
            $rp = $post['path'];
            $rrp = str_replace("\\", "/", realpath($rp));
            
            $path = Setting::checkPath($rrp, true);
            if($path !== true){
                $error = $path;
            }
        }
        echo json_encode($error);
    }
    
    public function actionNotif(){
        $error = null;
        try {
            NodeProcess::checkNode();
        } catch (CException $ex) {
            $error = $ex->getMessage();
        }
        
        echo json_encode($error);
    }
    
    public function actionLdap(){
        $error = null;
        try {
            $result = Yii::app()->ldap->user()->searchRaw("*");
        } catch (CException $ex) {
            $error = "No LDAP support for PHP";
        }
        echo json_encode($error);
    }

    public  function actionEmail(){
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        if (!empty($post)) {
            Email::initalSetting();
            touch(Email::$path.DIRECTORY_SEPARATOR.'email.lock');
            $this->setEmailSettings($post);
            
            Email::sendTestMail();
        }
        echo true;
    }
    
    public function actionCheckMail(){
        Email::initalSetting();
        $i = 0;
        while(file_exists(Email::$path.DIRECTORY_SEPARATOR.'email.lock') && $i = 10){
            sleep(1);
            $i++;
        }
        
        $error = null;
        $errorLog = file(Email::$errorLog);
        if(!empty($errorLog) || file_exists(Email::$path.DIRECTORY_SEPARATOR.'email.lock')){
            $error = "Failed to send Email";
        }
        echo json_encode($error);
    }
    
    public function setEmailSettings($data){
        Email::remove("email.transport");
        if($data['emailService'] == 'ses'){
            Email::set("email.transport.auth.accessKeyId",$data['emailAccessKeyId']);
            Email::set("email.transport.auth.secretAccessKey",$data['emailSecretAccessKey']);
            Email::set("email.transport.auth.rateLimit",$data['emailRateLimit']);
            Email::set("email.transport.auth.region",$data['emailRegion']);
        }elseif($data['emailService'] == 'gmail'){
            Email::set("email.transport.auth.user",$data['emailUser']);
            Email::set("email.transport.auth.pass",$data['emailPass']);
        }
        elseif($data['emailService'] == 'smtp'){
            Email::set("email.transport.auth.user",$data['emailUser']);
            Email::set("email.transport.auth.pass",$data['emailPass']);
            Email::set("email.transport.host",$data['emailHost']);
            Email::set("email.transport.port",$data['emailPort']);
        }
        
        if($data['emailService'] != 'none'){
            Email::set("email.from",$data['emailSender']);
        }else{
            Email::set("email.from",null);
        }
        
        if($data['emailService'] == 'smtp' || $data['emailService'] == 'none'){
            Email::set("email.transport.service",null);
        }else{
            Email::set("email.transport.service",$data['emailService']);
        }
    }
}