<?php

class RepoManager extends CComponent{
    public $repoPath;
    public function browse($dir = null){
        if(is_null($dir)){
            $dir = $this->repoPath;   
        }
        if($dir == $this->repoPath){
            $parent = null;
        }else{
            $parent = dirname($dir);
        }
        
        $list = array();
        
        foreach(glob($dir.DIRECTORY_SEPARATOR.'*') as $l){
            $itemName = explode(DIRECTORY_SEPARATOR, $l);
            $itemName = array_pop($itemName);
            if(substr($itemName, -5) != '.json'){
                if(is_dir($l)){
                    $list[] = array(
                        'name' => $itemName,
                        'type' => 'dir',
                        'path' => base64_encode($l)
                    );
                }else{
                    $list[] = array(
                        'name' => $itemName,
                        'type' => $this->fileType($l),
                        'path' => base64_encode($l)
                    );
                }
            }
        }
        usort($list,array('RepoManager','sortItem'));
        $count = count($list);
        
        $detail = array(
            'parent' => base64_encode($parent),
            'path' => base64_encode($dir),
            'type' => 'dir',
            'item' => $list,
            'count' => $count,
        );
        return $detail;
    }
    
    public function fileType($file){
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($finfo, $file);
        finfo_close($finfo);
        
        return $fileType;
    }
    
    public function fileInfo($file){
        
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
    
    public function editMeta(){
        
    }
    
    public function rename($oldName, $newName){
        
    }
    
    public function move($file, $path){
        
    }
    
    public function copy($file, $path){
        
    }
    
    public function upload($temp ,$file, $path){
        $json = JsonModel::load($path.DIRECTORY_SEPARATOR.$file.'.json');
        $json->default;
        move_uploaded_file($temp , $path .DIRECTORY_SEPARATOR. $file);
    }
    
    public function replace ($file){
        
    }
    
    public function download($path){
        
    }
    
    public function search(){
        
    }
	
    private static $_model = null;
    public static function model() {
    	if (is_null(self::$_model)) {
    		self::$_model = new RepoManager();
    	}
    	return self::$_model;
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
