<?php

class GenModelController extends Controller {

    public $templates = [];

    public function actionIndex() {
        if (isset($_GET['active'])) {
        }

        $this->renderForm('DevGenModel');
    }

    public function actionNewModel() {
        $this->templates['model.php'] = Yii::getPathOfAlias('application.components.codegen.templates');

        $model = new DevGenNewModel();
        $href  = "";

        if (isset($_POST['DevGenNewModel'])
            && $_POST['DevGenNewModel']['tableName'] != ""
            && $_POST['DevGenNewModel']['modelName'] != ""
        ) {
            $s         = $_POST['DevGenNewModel'];
            $tableName = $s['tableName'];
            $modelName = $s['modelName'];
            $module    = $s['module'] == 'plansys' ? 'application' : 'app';
            ModelGenerator::create($tableName, $modelName, $module);
            $href = Yii::app()->createUrl('/dev/genModel/index', ['active' => $s['module'] . "." . $modelName]);
        }

        $this->renderForm("DevGenNewModel", $model, ['href' => $href], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionDel($p) {
        $file = Yii::getPathOfAlias($p) . ".php";
        if (is_file($file)) {
            @unlink($file);
        }
    }

}
