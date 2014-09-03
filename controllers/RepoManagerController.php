<?php
class RepoManagerController extends Controller{
    public function actionIndex($path = null){
        $repo = new RepoManager;
        $item = $repo->browse();
        
        $properties = FormBuilder::load('RepoProperties');
        $properties->registerScript();
        
        $this->render('index', array(
            'item' => $item,
        ));
    }
    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }
    public function actionRenderProperties(){
        $properties = FormBuilder::load('RepoProperties');
        
        if ($this->beginCache('RepoProperties', array(
                    'dependency' => new CFileCacheDependency(
                            Yii::getPathOfAlias('application.forms.RepoProperties') . ".php"
            )))) {
            echo $properties->render();
            $this->endCache();
        }
    }
    public function actionDownload(){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        Yii::app()->request->xSendFile(base64_decode($post['path']), array('saveName'=>$post['name']));
        //Yii::app()->request->sendFile($post['name'], file_get_contents(base64_decode($post['path'])));
    }
    public function actionRemove(){
        
    }
    public function actionBrowse(){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $repo = new RepoManager;
        $item = $repo->browse(base64_decode($post['path']));
        echo json_encode($item);
    }
}