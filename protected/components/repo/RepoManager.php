<?php

class RepoManager extends CComponent{
    public $repoPath;
    
    public function listAll($dir = null){
        if(is_null($dir)){
            $dir = $this->repoPath;   
        }
        
        $list = glob($dir.DIRECTORY_SEPARATOR.'*');
        $count = count($list);
        
        $detail = array(
            'item' => $list,
            'count' => $count,
        );
        return $detail;
    }
    
    public function isDir($dir){
        
    }
    
    public function makeDir($dirName){
        
    }
    
    public function changeDir(){
        
    }
    
    public function __construct() {
        if(is_null($this->repoPath)){
            if(Setting::get("repo.path") == ''){
                $path = Setting::getRootPath().DIRECTORY_SEPARATOR.'repo';
                Setting::set("repo.path", $path);
                $this->repoPath = Setting::get("repo.path");
            }else{
                $this->repoPath = Setting::get("repo.path");
            }
        }
        if(Yii::app()->user->role != 'pde'){
            $module = Yii::app()->user->role;
            if(strpos($module, '.')==true){
                $module = explode('.', $module);
                $module = implode(DIRECTORY_SEPARATOR,$module);
            }
            $this->repoPath = $this->repoPath.DIRECTORY_SEPARATOR.$module;
        }  
    }
}
