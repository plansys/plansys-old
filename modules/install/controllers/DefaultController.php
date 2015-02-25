<?php

class DefaultController extends Controller {

    public function beforeAction($action) {
        if (!in_array(Setting::$mode, ["install", "init"])) {
            $this->redirect(['/site/login']);
            die();
        }

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
        $content = $this->renderPartial('index', [], true);
        $html = $this->renderPartial('_layout', [
            'content' => $content,
            'moduleUrl' => str_replace("static", "modules/install/views/default/", $this->staticUrl(""))
                ], true);

        echo $html;
    }

}
