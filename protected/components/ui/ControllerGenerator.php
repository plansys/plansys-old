<?php

class ControllerGenerator extends CComponent{
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
            if($m->class == $class_name){
                $action[] = array(
                    'name' => $m->name,
                    'param' => $reflection->getMethod($m->name)->getParameters(),
                );
            }
        }
        return $action;
    }
      
    public static function controllerName($class){
       $class_name = explode('.controllers.', $class);
       $class_name = $class_name[1];
       return $class_name;
    }
}
?>
