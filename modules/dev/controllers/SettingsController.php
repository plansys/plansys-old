<?php

class SettingsController extends Controller {
 
    public function actionIndex() {

        $this->renderForm("DevSettingsApplication");
    }

}