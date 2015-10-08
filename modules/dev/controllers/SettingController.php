<?php

class SettingController extends Controller {
    public function actionApp() {
        $model = new DevSettingApp;
        
        if (isset($_POST['DevSettingApp'])) {
            $model->attributes = $_POST['DevSettingApp'];
            $model->save();
        }
        
        $this->renderForm('DevSettingApp', $model);
    }
}