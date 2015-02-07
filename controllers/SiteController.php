<?php

class SiteController extends Controller {

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex() {
        if (Yii::app()->user->isGuest) {
            $this->redirect(array("login"));
        } else {
            $this->redirect(array("/" . lcfirst(strtolower(Yii::app()->user->role)) . '/default/index'));
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError($id = "") {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else {
                $shouldRender = false;
                switch ($error['code']) {
                    case 404:
                        $error = array(
                            'code' => 'Peringatan: Data / halaman tidak ditemukan',
                            'message' => 'Data yang ingin Anda lihat tidak dapat ditemukan. <br/>'
                            . 'Mohon periksa kembali URL yang ingin anda buka.<br/><br/>'
                            . 'Atau mungkin juga data yang ingin Anda akses sudah dihapus.'
                        );
                        $shouldRender = true;
                        break;
                }

                if ($shouldRender) {
                    $this->pageTitle = $error['code'];
                    $this->render('error', $error);
                }
            }
        } else {
            switch ($id) {
                case "integrity":
                    $error = array(
                        'code' => 'Peringatan: Integritas Data',
                        'message' => 'Anda tidak dapat menghapus data ini karena<br/> '
                        . 'data ini adalah referensi data lainnya. '
                    );
                    break;
                case "ldap_missing":
                    $error = array(
                        'code' => 'Peringatan: Login Tanpa Role',
                        'message' => 'Anda berhasil login ke sistem, '
                        . 'akan tetapi<br/>Anda belum memiliki Role pada sistem ini.'
                        . '<br/><br/>Mohon hubungi Administrator<br/> untuk mendapatkan Role pada sistem'
                    );
                    break;
            }
            if ($id != "") {
                $this->pageTitle = $error['code'];
                $this->render("error", $error);
            }
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

                ## update last_login user
                $sql = "update p_user set last_login = '" . date("Y-m-d H:i:s") . "' where id = " . Yii::app()->user->id;
                Yii::app()->db->createCommand($sql)->execute();

                ## audit trail tracker
                AuditTrail::login();

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
        ## audit trail tracker
        AuditTrail::logout();

        ## logout user
        Yii::app()->user->logout();

        $this->redirect(Yii::app()->homeUrl);
    }

}
