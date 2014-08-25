<?php

class ControllerGenerator extends CodeGenerator {

    //Helper UI
    public static function listAllFile() {
        $dir = Yii::getPathOfAlias('application.modules');
        $modules = glob($dir . DIRECTORY_SEPARATOR . "*");
        $files = array();
        foreach ($modules as $m) {
            $module = ucfirst(str_replace($dir . DIRECTORY_SEPARATOR, '', $m));
            $items = ControllerGenerator::listFile($module);
            $files[] = array(
                'module' => $module,
                'items' => $items
            );
        }
        return $files;
    }

    public static function listFile($module) {
        $dir = Yii::getPathOfAlias("application.modules.{$module}.controllers");
        $items = glob($dir . DIRECTORY_SEPARATOR . "*");
        foreach ($items as $k => $m) {
            $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $items[$k] = array(
                'name' => $m,
                'module' => $module,
                'class' => 'application.modules.' . lcfirst($module) . '.controllers.' . $m,
                'class_path' => 'application.modules.' . lcfirst($module) . '.controllers.'
            );
        }
        return $items;
    }

    public static function listMethod($class, $class_name) {
        $declaredClasses = get_declared_classes();

        if (!in_array($class_name, $declaredClasses))
            Yii::import($class, true);
        $reflection = new ReflectionClass($class_name);
        $methods = $reflection->getMethods();
        $action = array();
        foreach ($methods as $m) {
            if ($m->class == $class_name && !$reflection->getMethod($m->name)->isProtected() && !$reflection->getMethod($m->name)->isStatic() && self::isAction($m->name) == true
            ) {
                $action[] = array(
                    'name' => $m->name,
                    'param' => $reflection->getMethod($m->name)->getParameters(),
                    'form' => "",
                );
            }
        }
        return $action;
    }

    public static function isAction($method) {
        if (substr($method, 0, 6) == 'action')
            return true;
        else
            return false;
    }

    public static function checkUrl($class, $param, $method) {
        $module = explode('.modules.', $class);
        $module = explode('.controllers.', $module[1]);
        $moduleName = $module[0];
        $controllerName = $module[1];
        $controllerName = lcfirst(substr($controllerName, 0, -10));
        $url = null;
        if (empty($param)) {
            $method = lcfirst(substr($method, 6));
            $url = $moduleName . '/' . $controllerName . '/' . $method;
        }
        return $url;
    }

    public static function controllerPath($class) {
        $basePath = explode("protected", Yii::getPathOfAlias($class));
        $controllerPath = 'protected' . $basePath[1] . '.php';
        return $controllerPath;
    }

    public static function controllerName($class) {
        $className = explode('.controllers.', $class);
        $className = $className[1];
        return $className;
    }

    public static function moduleControllerName($class) {
        $module = explode('.modules.', $class);
        $module = explode('.controllers.', $module[1]);
        $moduleName = $module[0];
        $controllerName = self::controllerName($class);
        return array(
            'module' => $moduleName,
            'controller' => $controllerName
        );
    }

    //CodeGenerator
    protected $baseClass = "Controller";
    protected $basePath = "application.modules.{module}.Controllers";

    public function addActionIndex($actionName, $modelClass = null, $params) {
        $body = '
        $this->renderForm("' . $modelClass . '");';
        $this->updateFunction($actionName, $body, array('params' => $params));
    }

    public function addActionCreate($actionName, $modelClass = null, $params) {
        $body = '
        $model = new ' . $modelClass . ';
                
        if (isset($_POST["' . $modelClass . '"])) {
            $model->attributes = $_POST["' . $modelClass . '"];
            if ($model->save()) {
                $this->redirect(array("index"));
            }
        }
        $this->renderForm("' . $modelClass . '",$model);';
        $this->updateFunction($actionName, $body, array('params' => $params));
    }

    public function addActionUpdate($actionName, $modelClass = null, $params) {
        $body = '
        $model = $this->loadModel($id , "' . $modelClass . '");
                
        if (isset($_POST["' . $modelClass . '"])) {
            $model->attributes = $_POST["' . $modelClass . '"];
            if ($model->save()) {
                $this->redirect(array("index"));
            }
        }
        $this->renderForm("' . $modelClass . '",$model);';
        $this->updateFunction($actionName, $body, array('params' => $params));
    }

    public function addActionDelete($actionName, $modelClass = null, $params) {
        $body = '
        $this->loadModel($id , "' . $modelClass . '")->delete();';
        $this->updateFunction($actionName, $body, array('params' => $params));
    }

    public static function getTemplate() {
        return array(
            'default' => 'Default Action',
            '---' => '---',
            'index' => 'actionIndex',
            'create' => 'actionCreate',
            'update' => 'actionUpdate',
            'delete' => 'actionDelete',
        );
    }

    public function __construct($module, $class) {
        $this->basePath = str_replace('{module}', $module, $this->basePath);
        $this->load($class);
    }

}
