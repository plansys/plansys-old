<?php

class SettingsController extends Controller {
    public function actionIndex() {
       $model = new DevSettings;
       //$model->auditTrack = "view";
       $this->renderForm("DevSettings",$model);
    }
}

