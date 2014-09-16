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
    public function actionDownload($n,$f){
        Yii::app()->request->sendFile($n, file_get_contents(base64_decode($f)));
    }
    public function actionRemove(){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $file = base64_decode($post['file']);
        unlink($file);
        unlink($file.'.json');
    }
    public function actionBrowse(){
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $repo = new RepoManager;
        $item = $repo->browse(base64_decode($post['path']));
        echo json_encode($item);
    }
}