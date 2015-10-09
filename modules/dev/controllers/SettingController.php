<?php

class SettingController extends Controller {
    public function actionApp() {
        $model = new DevSettingApp;
        
        if (isset($_POST['DevSettingApp'])) {
            $model->attributes = $_POST['DevSettingApp'];
            $model->save();
            $this->flash('Application Setting Updated!');
        }
        
        $this->renderForm('DevSettingApp', $model);
    }
    
    public function actionDatabase() {
        $model = new DevSettingDatabase;
        
        if (isset($_POST['DevSettingDatabase'])) {
            $model->attributes = $_POST['DevSettingDatabase'];
            $model->save();
            $this->flash('Database Setting Updated!');
        }
        
        $this->renderForm('DevSettingDatabase', $model);
    }
}