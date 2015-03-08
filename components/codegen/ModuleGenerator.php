<?php

class ModuleGenerator extends CodeGenerator {

    protected $basePath = "app.modules";

    public static function listModuleForMenuTree() {
        $list    = [];
        $devMode = Setting::get('app.mode') === "plansys";
        if ($devMode) {
            $dir         = Yii::getPathOfAlias("application.modules") . DIRECTORY_SEPARATOR;
            $items       = glob($dir . "*", GLOB_ONLYDIR);
            $plansysList = [];
            foreach ($items as $k => $f) {
                $label     = str_replace($dir, "", $f);
                $classPath = $f . DIRECTORY_SEPARATOR . ucfirst($label) . 'Module.php';
                if (is_file($classPath)) {
                    $plansysList[$label] = [
                        'label'  => $label,
                        'icon'   => 'fa-empire',
                        'active' => @$_GET['active'] == 'plansys.' . $label,
                        'url'    => Yii::app()->controller->createUrl('/dev/genModule/index', [
                            'active' => 'plansys.' . $label
                        ])
                    ];
                }
            }

            $list[] = [
                'label' => 'Plansys',
                'items' => $plansysList
            ];
        }

        $dir     = Yii::getPathOfAlias("app.modules") . DIRECTORY_SEPARATOR;
        $items   = glob($dir . "*", GLOB_ONLYDIR);
        $appList = [];
        foreach ($items as $k => $f) {
            $label     = str_replace($dir, "", $f);
            $classPath = $f . DIRECTORY_SEPARATOR . ucfirst($label) . 'Module.php';
            if (is_file($classPath)) {

                $appList[$label] = [
                    'label'  => $label,
                    'icon'   => 'fa-empire',
                    'active' => @$_GET['active'] == 'app.' . $label,
                    'url'    => Yii::app()->controller->createUrl('/dev/genModule/index', [
                        'active' => 'app.' . $label
                    ])
                ];
            }
        }

        $list[] = [
            'label' => 'App',
            'items' => $appList
        ];


        return $list;
    }

    public function load($class) {
        $module = strtolower(str_replace("Module", "", $class));
        $this->basePath .= "." . $module;
        parent::load($class);
    }

    public function addFormPath($class) {
        $line = "'" . $this->basePath . ".forms." . $class . ".*',";

        $f            = $this->getFunctionBody('init');
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
