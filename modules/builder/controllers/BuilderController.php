<?php

class BuilderController extends Controller {

    private $vpath = 'application.modules.builder.views.builder';

    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }

    public function actionIndex() {
        ## register main builder js
        Asset::registerJS($this->vpath . '.builder');
        Asset::registerJS($this->vpath . '.index');

        ## register each view js
        Asset::registerJS("application.modules.builder.views.builder.form.init");
        Asset::registerJS("application.modules.builder.views.builder.model.init");
        Asset::registerJS("application.modules.builder.views.builder.code.init");
        
        $this->render('index');
    }

}
