<?php
class Report extends CComponent {
    public function getReportFile($reportFile){
        $ds = DIRECTORY_SEPARATOR;
        
        if($this->getModule()!== null){
            $controller = Yii::app()->controller->id;
            $basePath = Yii::app()->controller->module->basePath;
            $filePath = $basePath.$ds."reports".$ds.$controller.$ds.$reportFile.".php";
            
            if(is_file($filePath)){
                return $filePath;
            }else{
                return false;
            }
        }else{
            return false;
        }
        
    }
    
    public function getModule(){
	return Yii::app()->controller->module;
    }
    
    public function load($file,$_data = null){
        if(is_array($_data))
            extract($_data,EXTR_PREFIX_SAME,'data');
        else
            $data=$_data;
        $reportFile = $this->getReportFile($file);
        if($reportFile){
            ob_start();
            ob_implicit_flush(false);
            require($reportFile);
            return ob_get_clean();            
        }else{
            throw new CException(Yii::t('yii','{controller} cannot find the requested report "{view}".',
				array('{controller}'=>get_class(Yii::app()->controller), '{view}'=>$file)));
        }
    }
}
