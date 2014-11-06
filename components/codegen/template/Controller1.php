<?php

class TemplateController extends Controller {

    public function actionIndex() {
        $this->renderForm('TemplateIndex');
    }

    public function actionNew() {
        $model = new TemplateForm;
        if (isset($_POST["TemplateForm"])) {
            $model->attributes = $_POST["TemplateForm"];
            if ($model->save()) {
                Yii::app()->user->setFlash('info', 'Data berhasil disimpan');
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
                Yii::app()->user->setFlash('info', 'Data berhasil disimpan');
                $this->redirect(["index"]);
            }
        }
        $this->renderForm("TemplateForm", $model);
    }

    public function actionDelete() {
        $model = $this->loadModel($id, "TemplateForm");
        if (!is_null($model)) {
            Yii::app()->user->setFlash('info', 'Data berhasil dihapus');
            $model->delete();
        }

        $this->redirect(['index']);
    }

}
