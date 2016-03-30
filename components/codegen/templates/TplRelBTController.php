<?php

class TplRelBTController {
### TEMPLATE-START ###
    public function actionInsertRelModel() {
        $model  = new TemplateForm;
        $posted = false;
        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            if ($model->save()) {
                $posted = true;
            }
        }
        $this->renderForm("TemplateForm", $model, ['inserted' => $model->{$model->tableSchema->primaryKey}, 'posted' => $posted], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionUpdateRelModel($id) {
        $model  = $this->loadModel($id, 'TemplateForm');
        $posted = false;
        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            if ($model->save()) {
                $posted = true;
            }
        }
        $this->renderForm("TemplateForm", $model, ['posted' => $posted], [
            'layout' => '//layouts/blank'
        ]);
    }

    public function actionDeleteRelModel($id) {
        $model = $this->loadModel($id, 'TemplateForm');
        $model->delete();
        $this->renderForm("TemplateForm", $model, ['posted' => true, 'deleted' => true], [
            'layout' => '//layouts/blank'
        ]);
    }
### TEMPLATE-END ###
}