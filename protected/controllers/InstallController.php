<?php

class InstallController extends Controller{
    public function actionIndex(){
        $model = new AdminSetup;
        
        $this->renderForm('AdminSetup', $model);
    }
}
?>
