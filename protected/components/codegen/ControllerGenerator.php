<?php

class ControllerGenerator extends CodeGenerator {
    //Helper UI
    public static function listAllFile() {
        $dir = Yii::getPathOfAlias('application.modules');
        $modules = glob($dir . DIRECTORY_SEPARATOR . "*");
        $files = array();
        foreach ($modules as $m) {
            $module = ucfirst(str_replace($dir . DIRECTORY_SEPARATOR, '', $m));
            $items = ControllerGenerator::listFile($module);
            $files[] = array(
                'module' => $module,
                'items' => $items
            );
        }
        return $files;
    }

    public static function listFile($module) {
        $dir = Yii::getPathOfAlias("application.modules.{$module}.controllers");
        $items = glob($dir . DIRECTORY_SEPARATOR . "*");
        foreach ($items as $k => $m) {
            $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $items[$k] = array(
                'name' => $m,
                'module' => $module,
                'class' => 'application.modules.' . lcfirst($module) . '.controllers.' . $m,
                'class_path' => 'application.modules.' . lcfirst($module) . '.controllers.'
            );
        }
        return $items;
    }
    
    public static function listMethod($class,$class_name){
       $declaredClasses = get_declared_classes();
       
       if (!in_array($class_name, $declaredClasses))
                Yii::import($class, true);
        $reflection = new ReflectionClass($class_name); 
        $methods = $reflection->getMethods();
        $action = array();
        foreach ($methods as $m){    
            if($m->class == $class_name && !$reflection->getMethod($m->name)->isProtected()){
                $action[] = array(
                    'name' => $m->name,
                    'param' => $reflection->getMethod($m->name)->getParameters(),
                    'isStatic' => $reflection->getMethod($m->name)->isStatic(),
                );
            }
        }
        return $action;
    }
    public static function isAction($method){
        if(substr($method, 0, 6)=='action')
            return true;
        else
            return false;
    }
    public static function checkUrl($class,$isStatic,$param, $method){
        $module = explode('.modules.', $class);
        $module = explode('.controllers.',$module[1]);
        $module_name = $module[0];
        $controller_name = $module[1];
        $controller_name = lcfirst(substr($controller_name, 0,-10));
        $url = null;
        if(!$isStatic && empty($param) && self::isAction($method)==true){
            $method = lcfirst(substr($method, 6));
            $url = $module_name.'/'.$controller_name.'/'.$method;
        }
        return $url;
    }
      
    public static function controllerName($class){
       $class_name = explode('.controllers.', $class);
       $class_name = $class_name[1];
       return $class_name;
    }
    
    //CodeGenerator
    protected $baseClass = "Controller";
    protected $basePath = "application.modules.{module}.Controllers";

    public function generateClass($class = null) {
        $this->updateFunction('actionIndex', 'return array();');
    }

    public static function generate($module, $class) {
        $cg = new ControllerGenerator();
        $cg->basePath = str_replace('{module}', $module, $cg->basePath);
        $cg->load($class);
        $cg->updateFunction('actionIndex', 'return array();', array(
            'visibility' => 'protected',
            'params' => array(
                '$class = null',
            )
        ));

        
        return $cg;
    }

}
