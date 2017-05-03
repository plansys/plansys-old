<?php

Yii::import("application.modules.docs.forms.*");

class DefaultController extends Controller {
     public function actionIndex() {
          $this->renderForm("DocsIndex");
     }
}