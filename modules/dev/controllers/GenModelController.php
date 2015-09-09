<?php

class GenModelController extends Controller {

    public function actionIndex() {
        $model = new DevGenModel;
        $tableName = '';
        if (isset($_GET['active'])) {
            $model->load($_GET['active']);
            if ($model->generator) {
                $tableName = $model->generator->tableName;
            }
        }

        Asset::registerJS('application.static.js.lib.ace');

        $db = Setting::get('db');
        $this->renderForm('DevGenModel', $model, [
            'alterurl'  => "dev/default/adminer&username={$db['username']}&db={$db['dbname']}&create={$tableName}",
            'selecturl' => "dev/default/adminer&username={$db['username']}&db={$db['dbname']}&select={$tableName}"
        ]);
    }

    public function actionNew($name, $type, $table) {
        $name = ucfirst($name);
        $model = new DevGenModel;

        echo json_encode([
            'success' => false,
            'alias'   => "{$type}.{$name}"
        ]);
    }

}
