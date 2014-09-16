<?php

class SettingsController extends Controller {

    public function actionIndex($p) {

        $array = ['application', 'database', 'repository'];
        if (in_array(strtolower($p), $array)) {
            $this->renderForm("settings.DevSettings" . ucfirst(strtolower($p)));
        }
        else {
            throw new CHttpException(404, 'Not Found');
        }
    }

}