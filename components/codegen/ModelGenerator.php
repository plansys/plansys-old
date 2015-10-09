<?php

class ModelGenerator extends CComponent {

    public static function create($tableName, $modelName, $module) {
        Yii::import("application.framework.gii.*");
        Yii::import("application.framework.gii.generators.model.ModelCode");
        $mc            = new ModelCode();
        $mc->modelPath = $module . ".models";
        $mc->template  = 'model.php';

        $mc->tableName  = $tableName;
        $mc->baseClass  = 'ActiveRecord';
        $mc->modelClass = $modelName;
        $mc->prepare();
        $mc->save();
    }

    public static function getRuleList() {
        return [
            'required' => 'Required',
            'email' => 'Email',
            'boolean' => 'Boolean',
            'compare' => 'Compare',
            'date' => 'Date',
            'default' => 'Default',
            'exist' => 'Exist',
            'file' => 'File',
            'in' => 'Range',
            'length' => 'Length',
            'numerical' => 'Number',
            'match' => 'Regex Match',
            'unique' => 'Unique',
            'url' => 'Url',
            '---1' => '---',
            'safe' => 'Safe',
            'unsafe' => 'Unsafe',
            '---' => '---',
            'custom' => 'Custom'
        ];
    }

    public static function listTables() {
        $rawTables     = Yii::app()->db->createCommand("show tables")->queryColumn();
        $appTables     = [];
        $plansysTables = [];
        foreach ($rawTables as $key => $value) {
            if (strpos($value, "p_") === 0) {
                $plansysTables[$value] = $value;
            } else {
                $appTables[$value] = $value;
            }
        }

        $tables = [
            "App Tables" => $appTables,
        ];

        if (Setting::get('app.mode') == "plansys") {
            $tables["Plansys Tables"] = $plansysTables;
        }

        return $tables;
    }

    public static function listModels($includePlansys = false) {
        $dir    = Yii::getPathOfAlias("application.models");
        $appDir = Yii::getPathOfAlias("app.models");

        $devItems = glob($dir . DIRECTORY_SEPARATOR . "*");
        $appItems = glob($appDir . DIRECTORY_SEPARATOR . "*");
        $models   = [];

        $items = [];
        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $items[$m] = $m;
        }
        $models['App Model'] = $items;

        if (Setting::get('app.mode') == "plansys" || !!$includePlansys) {
            $items = [];
            foreach ($devItems as $k => $m) {
                $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
                $m = str_replace('.php', "", $m);

                $items[$m] = $m;
            }

            $models['Plansys Model'] = $items;
        }

        return $models;
    }

    public static function listMenuTree() {
        $dir    = Yii::getPathOfAlias("application.models");
        $appDir = Yii::getPathOfAlias("app.models");

        $devItems = glob($dir . DIRECTORY_SEPARATOR . "*");
        $appItems = glob($appDir . DIRECTORY_SEPARATOR . "*");

        $items  = [];
        $models = [];
        if (Setting::get('app.mode') == "plansys") {
            foreach ($devItems as $k => $m) {
                $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
                $m = str_replace('.php', "", $m);

                $devItems[$k] = [
                    'type' => 'plansys',
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
                'type' => 'plansys',
                'label' => 'Plansys',
                'items' => $devItems,
            ];
        }

        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $appItems[$k] = [
                'type' => 'app',
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
            'type' => 'app',
            'label' => 'App',
            'items' => $appItems,
        ];
        return $models;
    }

    public static function getFields($modelClass) {
        if (class_exists($modelClass) && method_exists($modelClass, 'model')) {
            return $modelClass::model()->getTableSchema()->getColumnNames();
        }
    }

}
