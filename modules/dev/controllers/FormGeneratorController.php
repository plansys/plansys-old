<?php

class FormGeneratorController extends Controller {

    public function actionIndex() {
        $templates = FormGenerator::listTemplates();
        $this->renderForm('DevGeneratorIndex', [
            'templates' => $templates
        ]);
    }

}
