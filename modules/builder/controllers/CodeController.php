<?php

class CodeController extends Controller {

    public function actionTree() {
        FormBuilder::renderUI('TreeView', [
            'name' => 'codetree',
                ], [
            'init' => 'startLoading()',
            'load' => 'startLoading()',
        ]);
    }

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

    public function actionProperties() {
        
    }

}
