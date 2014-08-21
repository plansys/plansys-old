<?php

class InstallController extends Controller {

    public function actionIndex() {
        if (!Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }
        $model = new AdminSetup;
        $model->attributes = Setting::get('db');
        if (isset($_POST['AdminSetup'])) {
            $model->attributes = $_POST['AdminSetup'];
            Setting::set('db', $model->attributes);
            if ($this->validateDB()) {
                if(file_exists(Setting::getRootPath() . '/installer/setup_db.lock')){
                    $sqlSetDb = "USE `".Setting::get('db.dbname')."`;";
                    $sqlContent = file_get_contents(Setting::getRootPath() . '/installer/database/plansys.sql');
                    $sqlContent = $sqlSetDb.' '.$sqlContent;

                    $conn = Setting::getDB();
                    $db = new PDO($conn['connectionString'], $conn['username'], $conn['password']);
                    $command = $db->prepare($sqlContent);
                    $command->execute();
                    
                    unlink(Setting::getRootPath() . '/installer/setup_db.lock');
                }
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
