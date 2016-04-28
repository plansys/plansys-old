<?php

class GenModelController extends Controller {

    public $templates = [];

    public function actionIndex() {
        $content = '';
        $name = '';
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
            $module = array_shift($path);
            $name = $path[count($path) -1];
            $path = implode(".", $path);
            $filePath = Yii::getPathOfAlias(($module == 'plansys' ? 'application' : 'app') . ".models." . $path) . ".php";

            $content = file_get_contents($filePath);
        }

        Asset::registerJS('application.static.js.lib.ace');
        $this->renderForm('DevGenModelIndex', [
            'content' => $content,
            'name' => $name,
            'models' => ModelGenerator::listModels(true)
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
            $fields = $schema[$table]->columns;
            $array  = [];
            foreach ($fields as $k=>$a) {
                $array[$k] = $k;
            }

            echo json_encode($array);
        }
    }

    public function actionTableList($conn) {
        echo json_encode(ModelGenerator::listTables($conn));
    }

    public function actionNewModel() {
        $this->templates['model.php'] = Yii::getPathOfAlias('application.components.codegen.templates');

        $model = new DevGenNewModel();
        $href  = "";

        if (isset($_POST['DevGenNewModel'])
            && $_POST['DevGenNewModel']['tableName'] != ""
            && $_POST['DevGenNewModel']['modelName'] != "") {
                
                $s         = $_POST['DevGenNewModel'];
                $conn      = $s['conn'];
                $tableName = $s['tableName'];
                $modelName = $s['modelName'];
                $module    = $s['module'] == 'plansys' ? 'application' : 'app';
                $options   = [
                    'conn' => $conn
                ];
    
                if ($s['softDelete'] == 'Yes') {
                    $options['softDelete'] = [
                        'column' => $s['softDeleteColumn'],
                        'value' => $s['softDeleteValue']
                    ];
                }
                
                ModelGenerator::create($tableName, $modelName, $module, $options);
                $href = Yii::app()->createUrl('/dev/genModel/index', ['active' => $s['module'] . "." . $modelName]);
        }

        $this->renderForm("DevGenNewModel", $model, [
            'href' => $href,
            'tableList' => ModelGenerator::listTables()
        ], [
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
