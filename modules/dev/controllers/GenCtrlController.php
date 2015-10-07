<?php

class GenCtrlController extends Controller {
    public function actionSave() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $part     = explode(".", $post['active']);
        $name     = array_pop($part);
        $module   = $part[0] == "plansys" ? "application" : "app";
        $filePath = "";

        if (count($part) == 1) {
            $filePath = Yii::getPathOfAlias("{$module}.controllers.{$name}") . ".php";
        } else if (count($part) == 2) {
            $filePath = Yii::getPathOfAlias("{$module}.modules.{$part[1]}.controllers.{$name}") . ".php";
        }

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
    }

    public function actionNewCtrl() {
        $model = new DevGenNewCtrl();

        $href = "";
        if (isset($_POST['DevGenNewCtrl'])) {
            $s             = $_POST['DevGenNewCtrl'];
            $s['ctrlName'] = $s['ctrlName'] . "Controller";
            $href          = Yii::app()->createUrl('/dev/genCtrl/index', ['active' => $s['module'] . "." . $s['ctrlName']]);

            ControllerGenerator::create($s['module'], $s['ctrlName']);
        }

        $this->renderForm('DevGenNewCtrl', $model, ['href' => $href], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionDel($p) {
        $file = Yii::getPathOfAlias($p) . ".php";
        if (is_file($file)) {
            @unlink($file);
        }
    }

    public function actionIndex() {

        $part    = [];
        $name    = "";
        $content = "";
        if (isset($_GET['active'])) {
            $part     = explode(".", $_GET['active']);
            $name     = array_pop($part);
            $module   = $part[0] == "plansys" ? "application" : "app";
            $filePath = "";
            if (count($part) == 1) {
                $filePath = Yii::getPathOfAlias("{$module}.controllers.{$name}") . ".php";
            } else if (count($part) == 2) {
                $filePath = Yii::getPathOfAlias("{$module}.modules.{$part[1]}.controllers.{$name}") . ".php";
            }
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
            }

            Asset::registerJS('application.static.js.lib.ace');

        }

        $this->renderForm("DevGenCtrlIndex", [
            'name' => $name,
            'content' => $content
        ]);
    }
}