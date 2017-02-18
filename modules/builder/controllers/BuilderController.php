<?php

class BuilderController extends Controller {

    public $vpath = 'application.modules.builder.views.builder';

    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }

    public function actionIndex() {
        ## register main builder js
        $this->render('index');
    }
}
