<?php

class UserController extends Controller {

    public function actionDelete($id) {
        $this->loadModel($id, "DevUserForm")->delete();
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, "DevUserForm");

        if (isset($_POST["DevUserForm"])) {
            $model->attributes = $_POST["DevUserForm"];
            if ($model->save()) {
                $this->redirect(array("index"));
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionNew() {
        $model = new DevUserForm;

        if (isset($_POST["DevUserForm"])) {
            $model->attributes = $_POST["DevUserForm"];
            if ($model->save()) {
                $this->redirect(array("index"));
            }
        }
        $this->renderForm("users.user.DevUserForm", $model);
    }

    public function actionIndex() {
        $this->renderForm("users.user.DevUserIndex");
    }

}