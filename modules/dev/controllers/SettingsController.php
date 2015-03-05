<?php

class SettingsController extends Controller {
    public function actionIndex() {
       $model = new DevSettings;
       $model->loadSettings();
       if(isset($_POST["DevSettings"])){   
           if($_POST["DevSettings"]["emailService"] !== 'none'){
               $emailSub = "DevSettingsEmail".ucfirst($_POST["DevSettings"]["emailService"]); 
               $_POST["DevSettings"] = array_merge($_POST["DevSettings"], $_POST[$emailSub]);
           }
           $model->attributes = $_POST["DevSettings"];
           $model->setSettings();
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
}