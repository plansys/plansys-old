<?php

class DefaultController extends Controller {
    public function actionNew() {

        $this->renderForm("");
    }

    public function actionIndex() {
        $this->redirect(array("/{$this->module->id}/forms"));
    }
    
}