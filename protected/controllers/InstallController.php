<?php

class InstallController extends Controller {

    public function actionIndex() {
        $model = new AdminSetup;
        $model->attributes = Setting::get('db');
        if (isset($_POST['AdminSetup'])) {
            $model->attributes = $_POST['AdminSetup'];
            Setting::set('db', $model->attributes);
            if ($this->validateDB()) {
                $sql_set_db = "USE `".Setting::get('db.dbname')."`;";
                $sql_content = file_get_contents(Setting::getRootPath() . '/installer/database/plansys.sql');
                $sql_content = $sql_set_db.' '.$sql_content;
                $command = Yii::app()->db->createCommand($sql_content);
                $command->execute();
                $this->redirect(array('site/login'));
            } else {
                $model->errors = array(
                    'driver' => array('Connection Failed')
                );
            }
        }
        $this->renderForm('AdminSetup', $model);
    }

    public function validateDB() {
        $conn = Setting::getDB();

        try {
            $db = new PDO($conn['connectionString'], $conn['username'], $conn['password']);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

}

?>
