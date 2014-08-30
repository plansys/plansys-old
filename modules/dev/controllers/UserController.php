<?php

class UserController extends Controller {
    public function actionTest() {
        $tes = new RepoManager();
        var_dump($tes->browse());
        die();
    }
    
    public function actionCreate() {
        $model = new DevUser;
        $model->username = "OPX";
        $model->nip = "qwdwqdqwdqwdqwd";

        if (isset($_POST['DevUser'])) {
            
            $model->attributes = $_POST['DevUser'];
            var_dump($model->attributes);
            die();
            
            if ($model->save()) {
                $this->redirect(array('index'));
            }
        }

        $this->renderForm('DevUser',  $model);
    }

}