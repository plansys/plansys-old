<?php

class DefaultController extends Controller {
    public function actionNew() {

        $this->renderForm("");
    }

    public function actionIndex() {
        $this->redirect(array("/{$this->module->id}/forms"));
    }
    
    public function actionAdminer() {
        $this->redirect('plansys/adminer.php');
    }
    
}