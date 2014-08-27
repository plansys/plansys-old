<?php

class UserController extends Controller {
    public function actionTest() {
        $tes = new RepoManager();
        var_dump($tes->listAll());
        die();
    }
    
    public function actionCreate() {
        $model = new AdminUser;
        $model->username = "OPX";
        $model->nip = "ASDASDASDAS";

        if (isset($_POST['AdminUser'])) {
            $model->attributes = $_POST['AdminUser'];
            if ($model->save()) {
                $this->redirect(array('index'));
            }
        }

        $this->renderForm('AdminUser',  $model);
    }

}