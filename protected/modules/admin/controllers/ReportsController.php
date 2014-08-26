<?php
require_once(Yii::app()->basePath . '/vendor/jasper/Jasper/Jasper.php');

class ReportsController extends Controller {
    public function actionIndex() {
		$reports_raw = Report::model()->findAll();
		$reports = Report::reportItems($reports_raw);
        
        $this->render('index', array(
            'reports' => $reports
        ));
        
    }
    
	public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }
	
	public function actionUpdate($path) {
        $this->layout = "//layouts/blank";
        
        try {
            $jasper = new \Jasper\Jasper('localhost:8080','jasperadmin','jasperadmin');
            $jasper->getServerInfo();
            
            $this->render('form', array(
                'server' => 'http://localhost:8080/jasperserver/flow.html?_flowId=viewReportFlow&ParentFolderUri=/reports&reportUnit=/reports/'.$path.'&decorate=no&j_username=jasperadmin&j_password=jasperadmin'
            ));
        } catch (Exception $e) {
            $this->render('errorcon', array());
        }
        
    }
    
    public function actionError() {
        $this->layout = "//layouts/blank";
        $this->render('error', array());
    }
}