<?php

##IMPORT-PLACEHOLDER##

class TplMasterController extends Controller {
    public function actionIndex() {
        $model = new TemplateIndex;
        if (!empty($_POST)) {
            ActiveRecord::batchPost('TemplateIndex',$_POST, 'dataSource1');
            $this->flash('Data Berhasil Di-update');
        }
        $this->renderForm('TemplateIndex', $model);
    }
}