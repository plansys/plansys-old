<?php

class ModelGenerator extends CodeGenerator {

    public $extendsFrom;
    public $tableName;

    public static function init($classAlias, $mode = 'load') {
        $path = explode('.', $classAlias);
        $class = ucfirst(array_pop($path));
        $path[count($path) - 1] = lcfirst($path[count($path) - 1]);

        $m = new ModelGenerator;
        $m->basePath = implode(".", $path);

        if (Helper::isValidVar($class)) {
            $m->load($class);

            if ($mode == 'create') {

            } else {
                $m->extendsFrom = get_parent_class($class);
                $m->tableName = $class::model()->tableName();
            }
            return $m;
        } else {
            return null;
        }
    }

    public static function listMenuTree() {
        $dir = Yii::getPathOfAlias("application.models");
        $appDir = Yii::getPathOfAlias("app.models");

        $devItems = glob($dir . DIRECTORY_SEPARATOR . "*");
        $appItems = glob($appDir . DIRECTORY_SEPARATOR . "*");

        $items = [];
        $models = [];
        if (Setting::get('app.mode') == "plansys") {
            foreach ($devItems as $k => $m) {
                $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
                $m = str_replace('.php', "", $m);

                $devItems[$k] = [
                    'label' => $m,
                    'icon' => 'fa fa-cube',
                    'class' => 'application.models.' . $m,
                    'class_path' => 'application.models',
                    'exist' => (class_exists($m)) ? 'yes' : 'no',
                    'type' => 'dev',
                    'active' => @$_GET['active'] == 'plansys.' . $m,
                    'url' => Yii::app()->controller->createUrl('/dev/genModel/index', [
                        'active' => 'plansys.' . $m,
                    ]),
                    'target' => 'col2',
                ];
            }

            $models[] = [
                'label' => 'Plansys',
                'items' => $devItems,
            ];
        }

        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $appItems[$k] = [
                'label' => $m,
                'icon' => 'fa fa-cube',
                'class' => 'app.models.' . $m,
                'class_path' => 'app.models',
                'exist' => (class_exists($m)) ? 'yes' : 'no',
                'type' => 'app',
                'active' => @$_GET['active'] == 'app.' . $m,
                'url' => Yii::app()->controller->createUrl('/dev/genModel/index', [
                    'active' => 'app.' . $m,
                ]),
                'target' => 'col2',
            ];
        }
        $models[] = [
            'label' => 'App',
            'items' => $appItems,
        ];
        return $models;
    }

    public static function getFields() {
        return [];
    }

}
