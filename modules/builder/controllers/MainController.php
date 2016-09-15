<?php

class MainController extends Controller {

    private $vpath = 'application.modules.builder.views.main';

    public function getViewPath() {
        parent::getViewPath();
        return Yii::getPathOfAlias($this->vpath);
    }

    public function actionIndex() {
        ## register main builder js
        Asset::registerJS($this->vpath . '.builder');
        Asset::registerJS($this->vpath . '.index');

        ## register each view js
        $rv   = Yii::getPathOfAlias("application.modules.builder.views");
        $dirs = glob($rv . "/*");
        foreach ($dirs as $dir) {
            $initFile = $dir . DIRECTORY_SEPARATOR . "init.js";
            if (is_file($initFile)) {
                Asset::registerJS(Helper::getAlias($initFile));
            }
        }

        $this->render('index');
    }

}
