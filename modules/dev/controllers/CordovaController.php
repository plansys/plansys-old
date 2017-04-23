<?php

class CordovaController extends Controller {
    public function actionIndex() {
        $this->renderForm("DevCordovaForm");
    }
}