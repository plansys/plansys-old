<?php

class UserController extends Controller {

    public function actionTest() {
        $em = Yii::app()->doctrine->entityManager;
        $meta = $em->getMetadataFactory()->getAllMetadata();
        $query = $em->createQuery("SELECT a FROM AuditTrail a");
        var_dump($query->getResult());
    }
    
    public function actionCreate() {
        $model = new AdminUser;
        $model->roles = 'OPX';
        $model->username = "OPX";
        $model->nip = "ASDASDASDAS";

        if (isset($_POST['AdminUser'])) {
            $model->attributes = $_POST['AdminUser'];
            if ($model->save()) {
                $this->redirect(array('index'));
            }
        }

        $this->renderForm('AdminUser', 'create', $model);
    }

}
