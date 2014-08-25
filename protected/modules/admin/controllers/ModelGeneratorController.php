<?php
class ModelGeneratorController extends Controller{

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }
    
    public function actionIndex(){
        $models = ModelGenerator::listAllFile();
        $this->render('index', array(
            'models' => $models
        ));
    }
    
    public function actionUpdate($class){
        $this->layout = "//layouts/blank";
        //$target = ControllerGenerator::moduleControllerName($class);
        //$method = ControllerGenerator::listMethod($class,$target['controller']);
        $className = array_pop(explode('.',$class));
        $model = new ModelGenerator($className);
        $modelDetail = $model->modelInfo;
        
        //$properties = FormBuilder::load('AdminControllerEditor');
        //$properties->registerScript();
        $this->render('form',array(
            'class' => $class,
            'modelDetail' => $modelDetail,
        ));
    } 
}
?>
