<?php

class GenModelController extends Controller {

    public $templates = [];

    public function actionIndex() {
        $content = '';
        $path    = [];
        if (isset($_GET['active'])) {
            $path = explode(".", $_GET['active']);
            if (count($path) < 2) {
                $ref      = new ReflectionClass($_GET['active']);
                $filename = $ref->getFileName();
                if (strpos($filename, Yii::getPathOfAlias('app')) === 0) {
                    $this->redirect(['/dev/genModel/index', 'active' => 'app.' . $_GET['active']]);
                } else if (strpos($filename, Yii::getPathOfAlias('application')) === 0) {
                    $this->redirect(['/dev/genModel/index', 'active' => 'plansys.' . $_GET['active']]);
                }
                throw new CHttpException(404);
                return false;
            }
            $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".models." . $path[1]) . ".php";

            $content = file_get_contents($filePath);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('DevGenModelIndex', [
            'content' => $content,
            'name' => count($path) > 1 ? $path[1] : ''
        ]);
    }

    public function actionSave() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $path     = explode(".", $post['active']);
        $filePath = Yii::getPathOfAlias(($path[0] == 'plansys' ? 'application' : 'app') . ".models." . $path[1]) . ".php";

        if (is_file($filePath)) {
            file_put_contents($filePath, $post['content']);
        }
    }

    public function actionFieldList($table) {
        $schema = Yii::app()->db->schema->tables;
        if (isset($schema[$table])) {
            echo  json_encode(array_keys($schema[$table]->columns));
        }
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
            $options   = [];

            if ($s['softDelete'] == 'Yes') {
                $options['softDelete'] = [
                    'column' => $s['softDeleteColumn'],
                    'value' => $s['softDeleteValue']
                ];
            }

            ModelGenerator::create($tableName, $modelName, $module, $options);
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
