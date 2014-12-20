<?php

class UserController extends Controller {

    public function actionNewRole() {
        $model = new DevRoleForm;

        if (isset($_POST["DevRoleForm"])) {
            $model->attributes = $_POST["DevRoleForm"];
            if ($model->save()) {
                $this->redirect(array("roles"));
            }
        }
        $this->renderForm("DevRoleForm", $model);
    }

    public function actionRole($id) {
        $model = $this->loadModel($id, "DevRoleForm");
        if (isset($_POST["DevRoleForm"])) {
            $model->attributes = $_POST["DevRoleForm"];
            if ($model->save()) {
                $this->redirect(array("roles"));
            }
        }
        $this->renderForm("users.role.DevRoleForm", $model);
    }

    public function actionRoles() {
        $this->renderForm("users.role.DevRoleIndex");
    }

    public function actionDel($id) {
        $user = $this->loadModel($id, "DevUserForm");
        $user->is_deleted = 1;
        $user->username = $user->username . " (DELETED - " . time() . ")";
        $user->update(['is_deleted', 'username']);
        Yii::app()->nfy->unsubscribe($user->id, null, true);
        Yii::app()->user->setFlash('info', 'User berhasil dihapus');
        $this->redirect(['/dev/user/index']);
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, "DevUserForm");
        if ($model->is_deleted) {
            throw new CHttpException(404);
        }
        
        if (isset($_POST["DevUserForm"])) {
            $userRoles = $model->userRoles;
            if (!isset($_POST['DevUserForm']['subscribed']))
                $_POST['DevUserForm']['subscribed'] = '';

            $model->attributes = $_POST["DevUserForm"];
            if ($model->save()) {
                Yii::app()->user->setFlash('info', 'User berhasil disimpan');
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionNew() {
        $model = new DevUserForm;

        if (isset($_POST["DevUserForm"])) {
            $model->attributes = $_POST["DevUserForm"];

            if (isset($_GET['u']) && isset($_GET['f'])) {
                $model->username = $_GET['u'];
                $model->fullname = $_GET['f'];
                $model->useLdap = true;
            }

            if ($model->save()) {
                $model->subscribed = "on";
                Yii::app()->user->setFlash('info', 'User Berhasil dibuat !');

                if (isset($_GET['ldap'])) {
                    $this->redirect(array("ldap"));
                } else {
                    $this->redirect(array("index"));
                }
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionLdapSearch($q = "*") {
        $result = Yii::app()->ldap->user()->searchRaw($q);
        echo json_encode($result);
    }

    public function actionLdap() {
        $this->renderForm("users.user.DevUserLdap", [
            'data' => Yii::app()->ldap->user()->searchRaw('*')
        ]);
    }

    public function actionIndex() {
        $this->renderForm("users.user.DevUserIndex", [
            'useLdap' => Yii::app()->user->useLdap
        ]);
    }

}
