<?php

class MigrationController extends Controller {

    public function actionIndex() {
        $model = new MigrationForm;

        if (isset($_POST['name'])) {
            if (trim($_POST['name']) == '') {
                Yii::app()->user->setFlash('info', 'Nama harus diisi');
                $model->isNew = true;
            } else {
                if (!$model->newMigration($_POST)) {
                    Yii::app()->user->setFlash('info', 'SQL Anda Salah !!');
                    $model->isNew = true;
                    $model->newsql = $_POST['newsql'];
                } else {
                    $this->redirect(array('index'));
                }
            }
        }

        $this->renderForm('migrationForm', $model);
    }

    public function actionRun($idx, $file) {
        sleep(1);
    }

}
