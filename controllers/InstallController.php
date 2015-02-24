<?php

class InstallController extends Controller {

    public $layout = "//install/_layout";

    public function filters() {
        return array('accessControl');
    }

    public function accessRules() {
        return array(
            array('allow',
                'expression' => 'in_array(Setting::$mode, ["install","init"])',
            ),
            array('deny'),
        );
    }

    public function beforeAction($action) {
        parent::beforeAction($action);

        $cs = Yii::app()->getClientScript();
        $root = Yii::app()->basePath;
        $path = str_replace([$root, "\\"], ["", "/"], $this->getViewPath());


        if (Setting::$mode != "init") {
            $path = "/plansys" . $path;
        }
        $cs->registerCssFile(Yii::app()->baseUrl . $path . '/install.css');

        return true;
    }

    public function actionIndex() {
        Installer::checkInstall();
        $this->render("index");
    }

}
