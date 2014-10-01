<?php

class SiteController extends Controller {

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $dblockPath = Yii::getPathOfAlias("application.installer.setup_db");
        if (!file_exists($dblockPath . '.lock')) {
            if (Yii::app()->user->isGuest) {
                $this->redirect(array("login"));
            }
            $this->redirect(array(lcfirst(strtolower(Yii::app()->user->roles)) . '/default/index'));
        } else {
            if (Setting::get("repo.path") == '') {
                $path = Setting::getRootPath() . DIRECTORY_SEPARATOR . 'repo';
                Setting::set("repo.path", $path);
            }
            $this->redirect(array("install/index"));
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the login page
     */
    public function actionLogin() {
        if (!Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->user->returnUrl);
        }

        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                $model = Yii::app()->user->model;
                $model->last_login = date("Y-m-d H:i:s");
                $model->update();
                
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }

        // display the login form
        $this->renderForm('LoginForm', $model);
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

}
