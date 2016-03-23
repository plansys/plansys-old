<?php

class TplRelMController {
### TEMPLATE-START ###
    public function actionInsertRelModel($id = "") {
        $model  = new TemplateForm;
        $posted = false;

        if ($id != "") {
            $model->foreignKey = $id;
        } else {
            $id = 999;
        }

        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            $model->foreignKey = $id;
            if ($model->insertMethod()) {
                $posted = true;
            }
        }
        $this->renderForm("TemplateForm", $model, ['inserted' => true, 'posted' => $posted], [
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

### TEMPLATE-END ###
}