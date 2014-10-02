<?php

class MigrationController extends Controller {

    public function actionIndex() {;
        $model = new MigrationForm;
        if (count($model->migrations) == 0) {
            $model->isNew = true;
        }

        if (isset($_POST['name'])) {
            if (trim($_POST['name']) == '') {
                Yii::app()->user->setFlash('info', 'Nama harus diisi');
                $model->isNew = true;
            } else {
                if (!$model->newMigration($_POST)) {
                    $model->isNew = true;
                    $model->newsql = $_POST['newsql'];
                } else {
                    $this->redirect(array('index'));
                }
            }
        }
        $this->renderForm('migrationForm', $model);
    }

    public function actionRun($id, $file, $store) {
        $model = new MigrationForm;
        $model->runFile($id, $file);
        
    }

}
