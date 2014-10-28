<?php

class RepoController extends Controller {

    public function actionIndex($path = null) {
        $_GET['currentDir'] = RepoManager::getModuleDir();
        $this->renderForm('Repo');
    }

    public function actionChangeDir($dir) {
        echo json_encode(RepoManager::model()->browse($dir));
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionRenderProperties() {
        $properties = FormBuilder::load('RepoProperties');

        if ($this->beginCache('RepoProperties', array(
                'dependency' => new CFileCacheDependency(Yii::getPathOfAlias('application.forms.RepoProperties') . ".php")
            ))) {
            echo $properties->render();
            $this->endCache();
        }
    }

    public function actionDownload($n, $f) {
        RepoManager::download($n, $f);
    }

    public function actionRemove() {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        $file = base64_decode($post ['file']);
        unlink($file);
        unlink($file . '.json');
    }


}
