<?php

class ModelController extends Controller {
    private $vpath = 'application.modules.builder.views.2_model';
    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }
    
    public function actionTree() {
        echo "TREE";
    }

    public function actionEditor() {
        echo $this->renderPartial("index");
    }

}
