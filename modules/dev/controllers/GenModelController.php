<?php

class GenModelController extends Controller {

    public function actionIndex() {
        $model = new DevGenModel;
        if (isset($_GET['active'])) {
            $model->load($_GET['active']);
        }

        Asset::registerJS('application.static.js.lib.ace');

        $this->renderForm('DevGenModel', $model);
    }

}
