<?php
class Report extends CComponent {
    public function render($reportFile,$model = null) {
        $this->load($reportFile,$model);
    }
    
    public function getReportFile($reportFile){
        $ds = DIRECTORY_SEPARATOR;
        
        $reportPath = null;
        if($this->getModule()!== null){
            $controller = Yii::app()->controller->id;
            $basePath = Yii::app()->controller->module->basePath;
            $reportPath = $basePath.$ds."reports".$ds.$controller.$ds.$reportFile.".php";
        }
        return $reportPath;
    }
    
    public function getModule(){
	return Yii::app()->controller->module;
    }
    
    public function load($reportFile,$model){
        $reportFile = $this->getReportFile($reportFile);
        if(!is_null($reportFile)){
            include($reportFile);
        }
    }
}
