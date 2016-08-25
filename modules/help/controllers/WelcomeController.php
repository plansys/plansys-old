<?php

Yii::import("application.modules.help.forms.*");

class WelcomeController extends Controller {
    
    public function actionIndex() {				
        $this->renderForm('HelpWelcome');
    }
}