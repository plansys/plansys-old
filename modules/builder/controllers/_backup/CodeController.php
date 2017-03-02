<?php

class CodeController extends Controller {

    private $vpath = 'application.modules.builder.views.1_code';
    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }  
    
    public function actionTree() {
        echo FormBuilder::renderUI('TreeView', [
                'name' => 'codetree',
            ], [
                'init' => 'col1.view.loading = true',
                'load' => 'col1.view.loading = false',
            ] 
        ); 
    } 

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

    public function actionProperties() {
        
    }

}
