<?php

class ControllerGeneratorController extends Controller {

    public $module = '';

    public function actionIndex() {
        $controllers = ControllerGenerator::listAllFile();
        $this->render('index', array(
            'controllers' => $controllers
        ));
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionRenderProperties($module) {
        $properties = FormBuilder::load('DevControllerEditor');
        $properties->model->module = $module;

        echo $properties->render();
    }

    public function actionRename($module, $class, $type) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $gen = new ControllerGenerator($module, $class, $type);
        if (isset($post['list'])) {
            $content = $post['list'];
            if (!is_null($content['param'])) {
                $param = explode(',', $content['param']);
                $gen->renameFunction($content['oldName'], $content['name'], $param);
            } else {
                $gen->renameFunction($content['oldName'], $content['name']);
            }
        }
    }

    public function actionSave($module, $class, $type) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $gen = new ControllerGenerator($module, $class, $type);

        if (isset($post['list'])) {
            $content = $post['list'];
            if ($content['name'] != '') {
                if (!is_null($content['param'])) {
                    $param = explode(',', $content['param']);
                } else {
                    $param = array();
                }
                
                if ($content['template'] == 'index' || $content['template'] == 'default') {
                    $gen->addActionIndex($content['name'], $content['form'], $param);
                } elseif ($content['template'] == 'indexWithPost') {
                    $gen->addActionIndexWithPost($content['name'], $content['form'], $param);
                } elseif ($content['template'] == 'update') {
                    $gen->addActionUpdate($content['name'], $content['form'], $param);
                } elseif ($content['template'] == 'create') {
                    $gen->addActionCreate($content['name'], $content['form'], $param);
                } elseif ($content['template'] == 'delete') {
                    $gen->addActionDelete($content['name'], $content['form'], $param);
                }
            }
        }
    }

    public function actionUpdate($class) {
        $this->layout = "//layouts/blank";
        $project = explode('.', $class);
        $type = $project[0];
        if ($type == 'application')
            $type = 'dev';

        $target = ControllerGenerator::moduleControllerName($class, $type);
        $gen = new ControllerGenerator($target['module'], $target['controller'], $type);
        $method = $gen->listMethod($class, $target['controller']);

        $properties = FormBuilder::load('DevControllerEditor');
        $properties->registerScript();

        $this->render('form', array(
            'method' => $method,
            'class' => $class,
            'type' => $type,
            'controller' => $target['controller'],
            'module' => $target['module'],
        ));
    }

}

?>