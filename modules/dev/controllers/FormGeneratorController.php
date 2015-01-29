<?php

class FormGeneratorController extends Controller {

    public function actionIndex() {
        $templates = FormGenerator::listTemplates();
        $this->renderForm('DevGeneratorIndex', [
            'templates' => $templates
        ]);
    }

    public function actionWizard($id) {
        Yii::import('application.components.codegen.' . $id . '.*');
        
        $class = ucfirst($id) . "Generator";
        $wizard = new $class;
        
        var_dump($wizard);
        die();
    }

}
