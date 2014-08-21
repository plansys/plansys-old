<?php

class ControllerGenerator extends CodeGenerator {

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
