<?php

class TemplateController extends Controller {

    public function actionIndex() {
        $model = new TemplateIndex;
        if (!empty($_POST)) {
            ActiveRecord::batchPost('TemplateIndex', $_POST, 'dataSource1');
            Yii::app()->user->setFlash('info', 'Data berhasil disimpan');
        }
        $this->renderForm('TemplateIndex');
    }

}
