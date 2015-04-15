<?php

class TesReportController extends Controller {

    public function actionIndex() {
        $model = User::model()->findByPk(1);
        $this->renderReport('tesReport/DevTesReport', array('model' => $model));
    }
}
