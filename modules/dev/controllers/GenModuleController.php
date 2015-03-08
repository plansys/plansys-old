<?php

class GenModuleController extends Controller {

    public function actionIndex() {
        $module = new DevGenModule;

        if (isset($_GET['active'])) {
            $module->load($_GET['active']);
        }

        $this->renderForm('DevGenModule', $module);
    }

}
