<?php

class CodeController extends Controller {
    public function actionTree() {
        FormBuilder::renderUI('TreeView', [
            'name'  => 'codetree',
        ]);
    }

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

    public function actionProperties() {
    }

}
