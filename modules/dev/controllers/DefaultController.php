<?php

class DefaultController extends Controller {

    public function actionNew() {

        $this->renderForm("");
    }

    public function actionIndex() {
        $this->redirect(array("/{$this->module->id}/forms"));
    }

    public function actionAdminer() {
        if (Yii::app()->user->isGuest) {
            throw new CHttpException(404);
        }

        $db = Setting::get('db');
        $params = [];
        if (count($_GET) > 1) {
            foreach ($_GET as $g => $i) {
                if ($g != "r") {
                    $params[] = $g . "=" . $i;
                }
            }
            $params = implode("&", $params);
        } else {
            $_GET['s'] = $db['server'];
            $_GET['u'] = $db['username'];
            $_GET['p'] = $db['password'];
            $_GET['db'] = $db['dbname'];
            $params = "username={$_GET['u']}&db={$_GET['db']}&p={$_GET['p']}&s{$_GET['s']}";
        }
        $this->render("adminer", [
            'params' => $params
        ]);
    }

}
