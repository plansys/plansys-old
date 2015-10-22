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
            if (!isset($_GET['s'])) {
                $_GET['s'] = $db['host'] . ":3306";
            }

            foreach ($_GET as $g => $i) {
                if ($g != "r") {
                    $params[] = $g . "=" . $i;
                }
            }

            $params[] = 'p=' . $db['password'];
            $params = implode("&", $params);
        } else {
            $_GET['s'] = $db['host'];
            $_GET['u'] = $db['username'];
            $_GET['p'] = $db['password'];
            $_GET['db'] = $db['dbname'];
            $params = "username={$_GET['u']}&db={$_GET['db']}&p={$_GET['p']}&s={$_GET['s']}";
        }
        
        var_dump("asek asek aja deh");

        $this->render("adminer", [
            'params' => $params
        ]);
    }

}
