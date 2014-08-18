<?php

class ControllerGeneratorController extends Controller{
    public function actionIndex(){
        $controllers = ControllerGenerator::listAllFile();
        
        $this->render('index', array(
            'controllers' => $controllers
        ));
    }
    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }
    public function actionUpdate(){
        $this->layout = "//layouts/blank";
        $class = $_GET['class'];
        $class_name = ControllerGenerator::controllerName($class);
        $method = ControllerGenerator::listMethod($class,$class_name);
        
        $this->render('form',array(
            'method' => $method,
            'class' => $class,
            'class_name' => $class_name,
        ));
    }
}
?>
