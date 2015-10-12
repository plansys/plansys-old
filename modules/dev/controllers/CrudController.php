<?php

class CrudController extends Controller {
    public $layout = '//layouts/blank';

    public function actionNew() {
        if (!empty($_POST)) {
            var_dump($_POST);
            die();
        }
        $this->renderForm('DevCrudMainForm');
    }
}