<?php

class ModuleGenerator extends CodeGenerator {

    protected $basePath = "app.modules";

    public function load($class) {
        $module = strtolower(str_replace("Module", "", $class));
        $this->basePath .= "." . $module;
        parent::load($class);
    }

    public function addFormPath($class) {
        $line = "'" . $this->basePath . ".forms." . $class . ".*',";

        $f = $this->getFunctionBody('init');
        $alreadyAdded = false;
        foreach ($f as $k => $l) {
            if (strpos($l, $line) !== false) {
                $alreadyAdded = true;
                break;
            }
        }
        if (!$alreadyAdded) {
            foreach ($f as $k => $l) {
                if (strpos($l, 'app.models.*') !== false) {
                    array_splice($f, $k + 1, 0, "\t\t\t" . $line);
                    break;
                }
            }

            array_pop($f);
            array_shift($f);

            $this->updateFunction('init', implode("\n", $f));

        }
    }

}
