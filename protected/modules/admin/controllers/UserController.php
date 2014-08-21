<?php

class UserController extends Controller {


    public function actionTest() {
        $em = Yii::app()->doctrine->entityManager;
        $query = $em->createQuery("SELECT a, b FROM UserRole a join a.user b");
        $a = $query->getSingleResult();
        var_dump($a);
        
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