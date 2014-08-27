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
    
    public function actionSave($class,$tableName){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
    }
    
    public function actionRenderProperties(){
        $properties = FormBuilder::load('DevModelEditor');
        
        if ($this->beginCache('DevModelProperties', array(
                    'dependency' => new CFileCacheDependency(
                            Yii::getPathOfAlias('application.modules.dev.forms.DevModelEditor') . ".php"
            )))) {
            echo $properties->render();
            $this->endCache();
        }
    }
    
    public function actionCreate($class){
        $gen = new ModelGenerator($class);
    }
    
    public function actionUpdate($class){
        $this->layout = "//layouts/blank";
        $className = array_pop(explode('.',$class));
        $model = new ModelGenerator($className);
        $modelDetail = $model->modelInfo;
        
        $this->render('form',array(
            'class' => $class,
            'modelDetail' => $modelDetail,
        ));
    } 
}
?>