<?php

class InstallController extends Controller {

    public $layout = "//install/_layout";

    public function filters() {
        return array('accessControl');
    }

    public function accessRules() {
        return array(
            array('allow',
                'expression' => '!Setting::isInstalled()',
            ),
            array('deny'),
        );
    }

    public function beforeAction($action) {
        parent::beforeAction($action);

        $baseUrl = Yii::app()->baseUrl;
        $cs = Yii::app()->getClientScript();
        $path = str_replace([Setting::getBasePath(), "\\"], ["", "/"], $this->getViewPath());
        $cs->registerCssFile(Yii::app()->baseUrl . $path . '/install.css');

        return true;
    }

    public function actionIndex() {
        $this->render("index");
    }

}
