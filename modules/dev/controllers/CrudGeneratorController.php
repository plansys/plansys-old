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

    public function generateModuleInternal($params) {
        extract($params);
        $module = strtolower($module);
        $moduleFile = <<<EOF
<?php
class $moduleClass extends CWebModule {
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

        file_put_contents($moduleClassPath, $moduleFile);
    }

    public function generateModule($params) {
        extract($params);
        $modulePath = Yii::getPathOfAlias('app.modules') . DIRECTORY_SEPARATOR . strtolower($module);
        if (!is_dir($modulePath)) {
            mkdir($modulePath, true);
        }

        $moduleClass = ucfirst(strtolower($module)) . 'Module';
        $moduleClassPath = $modulePath . DIRECTORY_SEPARATOR . $moduleClass . ".php";
        $params['moduleClassPath'] = $moduleClassPath;
        $params['modulePathAlias'] = "app.modules.{$module}";
        $params['moduleClass'] = $moduleClass;
        $params['moduleAlias'] = "app.modules.{$module}.{$moduleClass}";

        if (!is_file($moduleClassPath)) {
            $this->generateModuleInternal($params);
        } else {
            Yii::import($params['moduleAlias']);

            if (!method_exists($moduleClass, 'init')) {
                unlink($moduleClassPath);
                $this->generateModuleInternal($params);
            } else {
                $gen = new ModuleGenerator();
                $gen->load($moduleClass);
                $gen->addFormPath($tableName);
            }
        }

        return [
            'class' => $moduleClass,
            'path' => $modulePath,
            'alias' => $params['modulePathAlias'],
            'classPath' => $moduleClassPath
        ];
    }

    public function createFormFile($params, $type) {
        extract($params);
        $formClass = ucfirst($module) . $model;
        $formfile = <<<EOF
<?php

class {$formClass}{$type} extends {$model} {
    
}
EOF;
        file_put_contents($formPath . DIRECTORY_SEPARATOR . $formClass . $type . ".php", $formfile);


        return $moduleAlias . '.forms.' . $tableName . '.' . $formClass . $type;
    }

    public function actionCreateFormFile() {
        extract($_GET);
        $tableName = Helper::camelToUnderscore($model);
        $_GET['tableName'] = $tableName;

        ## create module path
        $moduleInfo = $this->generateModule($_GET);
        $_GET['moduleAlias'] = $moduleInfo['alias'];

        ## create directory
        $formPath = $moduleInfo['path'] . DIRECTORY_SEPARATOR . "forms" . DIRECTORY_SEPARATOR . $tableName;

        if (!is_dir($formPath)) {
            mkdir($formPath, 0777, true);
        }
        $_GET['formPath'] = $formPath;

        $file = [];
        ## create form file
        $file[] = $this->createFormFile($_GET, 'Form');

        ## create index file
        $file[] = $this->createFormFile($_GET, 'Index');

        echo json_encode($file);
    }

    public function actionGenerateController() {
        extract($_GET);
        $tableName = Helper::camelToUnderscore($model);
        $_GET['tableName'] = $tableName;

        ## create module path
        $moduleInfo = $this->generateModule($_GET);
        $_GET['moduleAlias'] = $moduleInfo['alias'];

        $dir = Yii::getPathOfAlias($_GET['moduleAlias'] . '.controllers');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $className = $model . 'Controller';
        $classPath = $_GET['moduleAlias'] . '.controllers.' . $className;
        $form1 = ucfirst($module) . $model . "Form";
        $form2 = ucfirst($module) . $model . "Index";

        $tfile = 'application.components.codegen.template.Controller1';
        $template = file_get_contents(Yii::getPathOfAlias($tfile) . ".php");
        $template = str_replace(['TemplateController', 'TemplateForm', 'TemplateIndex'], [$className, $form1, $form2], $template);

        file_put_contents(Yii::getPathOfAlias($classPath) . ".php", $template);

        echo 'CRUD Successfully Created!<br>' . CHtml::link('Try Here !!', ["/{$module}/" . lcfirst($model)]);
    }

}