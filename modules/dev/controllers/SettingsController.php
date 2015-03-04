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
}

