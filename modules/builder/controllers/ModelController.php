<?php

class ModelController extends Controller {

    public function actionTree() {
        echo "TREE";
    }

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

}
