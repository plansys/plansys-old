<?php

class DefaultController extends Controller {

    public function actionNew() {

        $this->renderForm("");
    }

    public function actionIndex() {
        $this->redirect(array("/{$this->module->id}/forms"));
    }

    public function actionAdminer() {
        if (Yii::app()->user->isGuest) {
            throw new CHttpException(404);
        }
        
        $db = Setting::get('db');
        
        $_GET['s'] = $db['server'];
        $_GET['u'] = $db['username'];
        $_GET['p'] = $db['password'];
        $_GET['db'] = $db['dbname'];
        
        $this->render("adminer");
    }

}
