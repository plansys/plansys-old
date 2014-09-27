<?php

class MigrationController extends Controller {

    public function actionIndex() {
        $model = new MigrationForm;
        
        $this->renderForm('migrationForm', $model);
    }

}
