<?php

class GenModuleController extends Controller {

    public function actionIndex() {
        $model = new DevGenModule;

        if (isset($_GET['active'])) {
            $model->load($_GET['active']);
            if (is_null($model->module)) {
                $model = new DevGenModule;
            }
        }

        $this->renderForm('DevGenModule', $model, [
            'module'      => $model,
            'controllers' => $model->getControllers()
        ]);
    }

    public function actionDelete($name, $module) {
        $model = new DevGenModule;
        $model->load($module . "." . $name);
        $model->delete();

        echo json_encode([
            'success' => true
        ]);
    }

    public function actionNew($name, $module) {
        $model = new DevGenModule;
        $model->create($module . '.' . $name);

        if (is_null($model->module)) {
            echo json_encode([
                'success' => false,
                'error'   => $model->error
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'alias'   => $module . '.' . $name
            ]);
        }
    }

}
