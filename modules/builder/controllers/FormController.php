<?php

class FormController extends Controller {
    public function actionTree() {
        echo "TREE";
    }

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

    public function actionProperties() {
        echo "Editor";
    }
}