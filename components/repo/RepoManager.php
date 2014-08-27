<?php

class RepoManager extends CComponent{
    public $repoPath;
    
    public function listAll($dir = null){
        if(is_null($dir)){
            if(Yii::app()->user->role == 'admin'){
                $dir = $this->repoPath;
            }else{
                $module = Yii::app()->user->role;
                $dir = $this->repoPath.'/'.$module;
            }
                
        }
        $list = glob($dir.'\*');
        $count = count($list);
        
        $detail = array(
            'item' => $list,
            'count' => $count,
        );
        return $detail;
    }
    
    public function makeDir($dirName){
        
    }
    
    public function changeDir(){
        
    }
    
    public function __construct() {
        if(is_null($this->repoPath)){
            if(Setting::get("repo.path") == ''){
                $path = Setting::getRootPath().'\repo';
                Setting::set("repo.path", $path);
                $this->repoPath = Setting::get("repo.path");
            }else{
                $this->repoPath = Setting::get("repo.path");
            }
        }
    }
}
