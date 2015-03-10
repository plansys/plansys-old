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
        Asset::registerJS('application.static.js.lib.ace');

        $this->renderForm('DevGenModule', $model, [
            'module'      => $model,
            'controllers' => $model->getControllers()
        ]);
    }

    public function actionSaveAc($active) {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);

        $model = new DevGenModule;
        $model->load($active);
        $model->module->updateAccessControl($post);
    }

    public function actionSaveImport($active) {
        $postdata = file_get_contents("php://input");
        $post = json_decode($postdata, true);

        $model = new DevGenModule;
        $model->load($active);
        $model->module->updateImport($post['code']);
    }

    public function actionGenImport($active) {
        $model = new DevGenModule;
        $model->load($active);
        $model->module->generateImport(true);
        $this->redirect(['/dev/genModule/index', 'active' => $active]);
    }

    public function actionDelete($name, $module) {
        $model = new DevGenModule;
        $model->load($module . '.' . ucfirst($name));
        $model->delete();

        echo json_encode([
            'success' => true
        ]);
    }

    public function actionNew($name, $module) {
        $model = new DevGenModule;
        $model->create(lcfirst($module) . '.' . ucfirst($name));

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
