<?php

##IMPORT-PLACEHOLDER##

class TplCrudController extends Controller {

    public function actionIndex() {
        $this->renderForm('TemplateIndex');
    }

    public function actionNew() {
        $model = new TemplateForm;
        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            if ($model->save()) {
                $this->flash('Data Berhasil Disimpan');
                $this->redirect(['index']);
            }
        }

        $this->renderForm("TemplateForm", $model);
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id, "TemplateForm");
        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            if ($model->save()) {
                $this->flash('Data Berhasil Disimpan');
                $this->redirect(["index"]);
            }
        }
        $this->renderForm("TemplateForm", $model);
    }

    public function actionDelete($id) {
        if (strpos($id, ',') > 0) {
            ActiveRecord::batchDelete("TemplateForm", explode(",", $id));
            $this->flash('Data Berhasil Dihapus');
        } else {
            $model = $this->loadModel($id, "TemplateForm");
            if (!is_null($model)) {
                $this->flash('Data Berhasil Dihapus');
                $model->delete();
            }
        }


        $this->redirect(['index']);
    }

}
