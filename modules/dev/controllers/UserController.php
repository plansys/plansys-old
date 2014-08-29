<?php

class UserController extends Controller {
    public function actionTest() {
        $tes = new RepoManager();
        var_dump($tes->listAll());
        die();
    }
    
    public function actionCreate() {
        $model = new DevUser;
        $model->username = "OPX";
        $model->nip = "ASDASDASDAS";

        if (isset($_POST['DevUser'])) {
            
            $model->attributes = $_POST['DevUser'];
            if ($model->save()) {
                $this->redirect(array('index'));
            }
        }

        $this->renderForm('DevUser',  $model);
    }

}