<?php

class SettingsController extends Controller {
    public function actionIndex() {
       $model = new DevSettings;
       $model->loadSettings();
       if(isset($_POST["DevSettings"])){
           var_dump($_POST["DevSettings"]);die();
       }
       $this->renderForm("DevSettings",$model);
    }
}

