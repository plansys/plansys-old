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
    
    public function actionTheme() {
        $model = new DevSettingTheme;
        
        if (isset($_GET['id'])) {
            $model->active = $_GET['id'] === '' ? null : $_GET['id'];
            $model->save();
            $this->flash('Theme Updated!');
            $this->redirect(['/dev/setting/theme']);
        }
        
        $this->renderForm('DevSettingTheme', $model);
    }
    
    public function actionDatabase() {
        $model = new DevSettingDatabase;
        $posted = false;
        
        if (isset($_POST['DevSettingDatabase'])) {
            $model->attributes = $_POST['DevSettingDatabase'];
            if ($model->save()) {
                $posted = true;
                $this->flash('Database Setting Updated!');
            }
        }
        
        $this->renderForm('DevSettingDatabase', $model, [
            'posted' => $posted
        ]);
    }
    
    public function actionEmail() {
        $model = new DevSettingEmail;
        
        if (isset($_POST['DevSettingEmail'])) {
            $model->attributes = $_POST['DevSettingEmail'];
            $model->save();
            $this->flash('Email Setting Updated!');
        }
        
        $this->renderForm('DevSettingEmail', $model);
    }
    
    public function actionLdap() {
        $model = new DevSettingLdap;
        
        if (isset($_POST['DevSettingLdap'])) {
            $model->attributes = $_POST['DevSettingLdap'];
            $model->save();
            $this->flash('LDAP Setting Updated!');
        }
        
        $this->renderForm('DevSettingLdap', $model);
    }
    
}