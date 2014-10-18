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

    public function actionDelete($id) {
        $this->loadModel($id, "DevUserForm")->delete();
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, "DevUserForm");

        if (isset($_POST["DevUserForm"])) {
            $userRoles = $model->userRoles;
            if (!isset($_POST['DevUserForm']['subscribed']))
                $_POST['DevUserForm']['subscribed'] = '';
            
            $model->attributes = $_POST["DevUserForm"];

            if ($model->save()) {
                foreach ($_POST['DevUserForm']['userRoles'] as $k => $u) {
                    $_POST['DevUserForm']['userRoles'][$k]['user_id'] = $model->id;
                }
                ActiveRecord::batch('UserRole', $_POST['DevUserForm']['userRoles'], $userRoles);

                Yii::app()->user->setFlash('info', 'User berhasil disimpan');
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionNew() {
        $model = new DevUserForm;

        if (isset($_POST["DevUserForm"])) {
            $model->attributes = $_POST["DevUserForm"];
            if ($model->save()) {
                foreach ($_POST['DevUserForm']['userRoles'] as $k => $u) {
                    $_POST['DevUserForm']['userRoles'][$k]['user_id'] = $model->id;
                }
                ActiveRecord::batch('UserRole', $_POST['DevUserForm']['userRoles'], $userRoles);

                $this->redirect(array("index"));
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionIndex() {
        $this->renderForm("users.user.DevUserIndex");
    }

}
