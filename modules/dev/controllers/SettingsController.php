<?php

class SettingsController extends Controller {
    public function actionIndex() {
       $model = new DevSettings;
       $model->loadSettings();
       $this->renderForm("DevSettings",$model);
    }
}

