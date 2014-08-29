<?php

class RepoManager extends CComponent{
    public $repoPath;
    
    public function listAll($dir = null){
        if(is_null($dir)){
            $dir = $this->repoPath;   
        }
        
        
        $list = array();
        
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*') as $l){
            $itemName = explode(DIRECTORY_SEPARATOR, $l);
            $itemName = array_pop($itemName);
            if(is_dir($l)){
                $list[] = array(
                    'name' => $itemName,
                    'type' => 'dir',
                    'path' => $l
                );
            }else{
                $list[] = array(
                    'name' => $itemName,
                    'type' => $this->fileInfo($l),
                    'path' => $l
                );
            }
        }
        usort($list,array('RepoManager','sortItem'));
        $count = count($list);
        
        $detail = array(
            'parent' => $dir,
            'item' => $list,
            'count' => $count,
        );
        return $detail;
    }
    
    public function fileInfo($file){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file);
        finfo_close($finfo);
        
        return $fileType;
    }
    
    protected static function sortItem($a, $b){
        if ($a['type']=='dir'){
            if($b['type']=='dir')
                return strcmp($a['name'],$b['name']);
            else
                return -1;
        }else if($a['type']!='dir'){
            if($b['type']=='dir')
                return 1;
            else{
                if ($a['type']!=$b['type'])
                    return strcmp($a['type'],$b['type']);

                return strcmp($a['name'],$b['name']);
            }     
        }
    }
    
    public function __construct() {
        if(Setting::get("repo.path") == ''){
            $path = Setting::getRootPath().DIRECTORY_SEPARATOR.'repo';
            Setting::set("repo.path", $path);
            $this->repoPath = Setting::get("repo.path");
        }else{
            $this->repoPath = Setting::get("repo.path");
        }
        
        if(Yii::app()->user->role != 'admin' && Yii::app()->user->role != 'dev'){
            $module = Yii::app()->user->role;
            if(strpos($module, '.')==true){
                $module = explode('.', $module);
                $module = implode(DIRECTORY_SEPARATOR,$module);
            }
            $this->repoPath = $this->repoPath.DIRECTORY_SEPARATOR.$module;
            if(!file_exists($this->repoPath)){
                mkdir($this->repoPath, 0777, true);
            }
        }  
    }
}
