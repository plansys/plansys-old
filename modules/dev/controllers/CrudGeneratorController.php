<?php


class CrudGeneratorController extends Controller {

    public function actionIndex() {
        $this->renderForm('DevCrudForm');
    }

    public function actionCreateModelFile() {
        extract($_GET);

        ## create file
        $path = Yii::getPathOfAlias('app.models') . DIRECTORY_SEPARATOR . $model . ".php";
        if (is_file($path)) {
            unlink($path);
        }

        touch($path);
        echo $path;
    }

    public function generateModule($params) {
        extract($params);

        $modulePath = Yii::getPathOfAlias('app.modules') . DIRECTORY_SEPARATOR . strtolower($module);
        if (!is_dir($modulePath)) {
            mkdir($modulePath, true);
        }

        $moduleClass = ucfirst(strtolower($module)) . 'Module';
        $moduleClassPath = $modulePath . DIRECTORY_SEPARATOR . $moduleClass . ".php";
        if (!is_file($moduleClassPath)) {
            $moduleFile = <<<EOF
<?php

class GlobalModule extends CWebModule {
    public function init() {
        // import the module-level models and components
        \$this->setImport(array(
            'app.models.*',
            'app.modules.$module.controllers.*',
            'app.modules.$module.forms.*',
            'app.moduels.$module.forms.$tableName.*',
            'app.modules.$module.components.*',
        ));
    }

    public function beforeControllerAction(\$controller, \$action) {
        if (parent::beforeControllerAction(\$controller, \$action)) {
            return true;
        } else
            return false;
    }

}

EOF;
            file_put_contents($moduleClassPath, $modulePath);
        } else {
            $gen->load($moduleClass);
        }

        return [
            'class' => $moduleClass,
            'path' => $moduleClassPath
        ];
    }

    public function actionCreateFormFile() {
        extract($_GET);

        ## create module path
        $moduleInfo = $this->generateModule($_GET);

        print_r($moduleInfo);
    }

}
