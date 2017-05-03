<?php

Yii::import("application.modules.docs.forms.*");

class WelcomeController extends Controller {
    
    public function actionIndex() {				
        $this->renderForm('DocsWelcome');
    }
}