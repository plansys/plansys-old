<?php

class DefaultController extends Controller {

    public function beforeAction($action) {
        if (!in_array(Setting::$mode, ["install", "init"])) {
            $this->redirect(['/site/login']);
            die();
        }

        parent::beforeAction($action);

        $cs = Yii::app()->getClientScript();
        $root = Yii::app()->basePath;
        $path = str_replace([$root, "\\"], ["", "/"], $this->getViewPath());

        if (Setting::$mode != "init") {
            $path = "/plansys" . $path;
        }
        $cs->registerCssFile(Yii::app()->baseUrl . $path . '/install.css');

        return true;
    }

    public function getModuleUrl() {
        return str_replace("static", "modules/install/views/default/", $this->staticUrl(""));
    }

    public function actionIndex($msg = null) {
        $content = $this->renderPartial('index', [
            'msg' => $msg
                ], true);
        $html = $this->renderPartial('_layout', ['content' => $content], true);
        echo $html;
    }

    public function actionFinish() {
        if (isset($_GET['s'])) {
            echo Setting::$mode;
            die();
        }
        
        
        $this->renderForm('InstallFinishForm');
    }

    public function actionUser() {
        $model = new InstallUserForm;
        $error = false;
        $success = 'n';
        $model->fullname = "Developer";
        $model->username = "dev";

        if (isset($_POST['InstallUserForm'])) {
            $model->attributes = $_POST['InstallUserForm'];
            if ($model->validate()) {

                ActiveRecord::execute("
                set foreign_key_checks = 0;
                UPDATE `p_user` SET
                    `id` = '1',
                    `nip` = '-',
                    `fullname` = '{$model->fullname}',
                    `email` = '-',
                    `phone` = '-',
                    `username` = '{$model->username}',
                    `password` = md5('{$model->password}'),
                    `last_login` = '2015-02-26 07:06:32',
                    `is_deleted` = '0'
                WHERE `id` = '1';
                ");

                Installer::createIndexFile("running");
                $this->redirect(['/install/default/finish']);
            }
        }

        $this->renderForm('InstallUserForm', $model, [
            'error' => $error,
            'success' => $success
        ]);
    }

    public function actionDb() {
        $model = new InstallDbForm();
        $model->host = Setting::get('db.host');
        $model->username = Setting::get('db.username');
        $model->password = Setting::get('db.password');
        $model->dbname = Setting::get('db.dbname');
        $error = false;

        if (isset($_POST['InstallDbForm'])) {
            $model->attributes = $_POST['InstallDbForm'];

            if ($model->validate()) {
                $mysqlConnection = @mysql_connect($model->host, $model->username, $model->password);
                if (!$mysqlConnection) {
                    $error = "Mysql connection failed, please your credential:";
                } else {
                    $result = mysql_select_db($model->dbname, $mysqlConnection);
                    if (!$result) {
                        $error = "Failed to select database: \"{$model->dbname}\"";
                    }
                }

                if (!$error) {
                    Setting::set('db.host', $model->host);
                    Setting::set('db.username', $model->username);
                    Setting::set('db.password', $model->password);
                    Setting::set('db.dbname', $model->dbname);

                    if ($model->resetdb == "yes") {
                        Installer::createIndexFile("install");
                        Installer::resetDB();
                        $this->redirect(['/install/default/user']);
                    } else {
                        Installer::createIndexFile("running");
                        $this->redirect(['/install/default/finish']);
                    }
                }
            }
        }

        $this->renderForm('InstallDbForm', $model, [
            'error' => $error
        ]);
    }

}
