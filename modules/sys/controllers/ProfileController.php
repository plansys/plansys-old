<?php

class ProfileController extends Controller {

    public function actionIndex() {
        Yii::import('application.modules.dev.forms.users.user.DevUserForm');
        $model = $this->loadModel(Yii::app()->user->id, "DevUserForm");

        if (isset($_POST["DevUserForm"])) {
            $model->attributes = $_POST["DevUserForm"];
            $model->subscribed = "on";

            if ($model->save()) {
                Yii::app()->user->setFlash('info', 'Profil Anda Tersimpan.');
            }
        }

        if (isset($_GET['e']) && $_GET['e'] && !isset($_POST["DevUserForm"])) {
            $model->addError('email', 'Isi e-mail untuk menerima notifikasi');
        }

        $this->renderForm("DevUserForm", $model);
    }

    public function actionChangeUser($id) {
        $roles = Yii::app()->user->model->roles;
        foreach ($roles as $r) {
            if ($r['id'] == $id) {
                $rootRole = Helper::explodeFirst(".", $r['role_name']);
                Yii::app()->user->setState('fullRole', $r['role_name']);
                Yii::app()->user->setState('role', $rootRole);
            }
        }

        $this->redirect(Yii::app()->request->urlReferrer ? Yii::app()->request->urlReferrer : Yii::app()->user->returnUrl );
    }

}
