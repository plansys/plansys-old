<?php
class ControllerGeneratorController extends Controller{
    public function actionTe() {

        $this->renderForm("AdminTes");
    }

    public function actionT() {

        $this->renderForm("AdminTes");
    }

    public function actionNew() {

        $this->renderForm("AdminTes");
    }

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
    
    public function actionRenderProperties(){
        $properties = FormBuilder::load('AdminControllerEditor');
        
        if ($this->beginCache('AdminControllerProperties', array(
                    'dependency' => new CFileCacheDependency(
                            Yii::getPathOfAlias('application.modules.admin.forms.AdminControllerEditor') . ".php"
            )))) {
            echo $properties->render();
            $this->endCache();
        }
    }
    
    public function actionSave($module ,$class){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $gen = new ControllerGenerator($module, $class);
        if (isset($post['list'])) {
            $content = $post['list'];
            if($content['name']!=''){
                if($content['template']=='index' || $content['template']=='default'){
                    $gen->addActionIndex($content['name'], $content['form']);
                }elseif ($content['template']=='update') {
                    $gen->addActionUpdate($content['name'], $content['form']);
                }elseif ($content['template']=='create') {
                    $gen->addActionCreate($content['name'], $content['form']);
                }elseif ($content['template']=='delete') {
                    $gen->addActionDelete($content['name'], $content['form']);
                }
            }
        }
        var_dump($post['list']['name'],$module,$class);
    }
    
    public function actionUpdate($class){
        $this->layout = "//layouts/blank";
        $target = ControllerGenerator::moduleControllerName($class);
        $method = ControllerGenerator::listMethod($class,$target['controller']);
        
        $properties = FormBuilder::load('AdminControllerEditor');
        $properties->registerScript();
        
        $this->render('form',array(
            'method' => $method,
            'class' => $class,
            'controller' => $target['controller'],
            'module' =>$target['module'],
        ));
    }    
}
?>