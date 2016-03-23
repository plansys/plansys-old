<?php

class TplRelMMController extends Controller {
### TEMPLATE-START ###
    public function actionChooseRelModel($id = 0) {
        $this->renderForm("TemplateChooseForm", null, [], [
            'layout' => '//layouts/blank'
        ]);
    }
### TEMPLATE-END ###
}