<?php
        
class ImportController extends Controller {
    public function beforeAction($action) {
        if (isset($_GET['m'])) {
            
            $m = explode(".", $_GET['m']);
            $model = @$m[0];
            $submodel = @$m[1];
            
            if (class_exists($model) && is_subclass_of($model, 'ActiveRecord')) { 
                return true;
            }
        }
        throw new CHttpException(404);
    }
    
    public function actionIndex() {
        $model = new SysImportData;
        $submodel = '';
        if (isset($_POST["SysImportData"])) {
            $_POST['SysImportData']['model'] = $_GET['m'];
            ServiceManager::start('ImportData', $_POST["SysImportData"]);
            return;
        }
        
        $options = [];
        
        if (@$_GET['mode'] == "blank") {
            $options['layout'] = "//layouts/blank";
        }
        
        $this->renderForm("SysImportData", $model, [
            's' => $submodel
        ], $options);
    }
    
    public function actionDownloadTemplate() {
        Export::downloadSample($_GET['m']);
    }
}