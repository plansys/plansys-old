<?php

class FormGeneratorController extends Controller {

    public function actionIndex() {
        $templates = FormGenerator::listTemplates();
        $this->renderForm('DevGeneratorIndex', [
            'templates' => $templates
        ]);
    }

    public function actionWizard($id) {
        Yii::import('application.components.codegen');
    }

}
