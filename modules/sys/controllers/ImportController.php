<?php
        
class ImportController extends Controller {
    public function beforeAction($action) {
        if (isset($_GET['m'])) {
            if (class_exists($_GET['m']) && is_subclass_of($_GET['m'], 'ActiveRecord')) { 
                return true;
            }
        }
        throw new CHttpException(404);
    }
    
    public function actionIndex() {
        $model = new SysImportData;
        if (isset($_POST["SysImportData"])) {
            $_POST['SysImportData']['model'] = $_GET['m'];
            ServiceManager::start('ImportData', $_POST["SysImportData"]);
            return;
        }
        
        $this->renderForm("SysImportData", $model);
    }
    
    public function actionDownloadTemplate() {
        Export::downloadSample($_GET['m']);
    }
}