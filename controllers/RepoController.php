<?php

class RepoController extends Controller {

    public function actionIndex($path = null) {
        $_GET['currentDir'] = RepoManager::getModuleDir();
        if (isset($_GET['dir'])) {
            $dir                = trim(trim($_GET['dir'], '/'), '\\');
            $_GET['currentDir'] = $_GET['currentDir'] . DIRECTORY_SEPARATOR . $dir;
        }
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
        $startCache = $this->beginCache('RepoProperties', [
            'dependency' =>
            new CFileCacheDependency(Yii::getPathOfAlias('application.forms.RepoProperties') . ".php")
        ]);
        if ($startCache) {
            echo $properties->render();
            $this->endCache();
        }
    }

    public function actionDownload($n, $f) {
        RepoManager::download($n, $f);
    }

    public function actionRemove() {
        $postdata = file_get_contents("php://input");
        $post     = CJSON::decode($postdata);
        $file     = base64_decode($post ['file']);
        unlink($file);
        unlink($file . '.json');
    }

}
