<?php

class ModelGenerator extends CComponent {

    public static function create($tableName, $modelName, $module, $options = []) {
        $mc               = new ModelGeneratorCode();
        $mc->modelPath    = $module . ".models";
        
        if (isset($options['conn']) && $options['conn'] != 'db') {
            $mc->modelPath    = $module . ".models.{$options['conn']}"; 
            $mc->connectionId = $options['conn'];
        }
        
        $modelDir = Yii::getPathOfAlias($mc->modelPath);
        if (!is_dir($modelDir)) {
            mkdir($modelDir, 0777, true);
        }
        
        $mc->template     = 'TplModel.php';

        $mc->tableName  = $tableName;
        $mc->baseClass  = 'ActiveRecord';
        $mc->modelClass = $modelName;
        if (isset($options['conn'])) {
            unset($options['conn']);
        }
        $mc->options    = $options;

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

    public static function listTables($conn = 'db') {
        $rawTables     = array_keys(Yii::app()->{$conn}->schema->tables);;
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

        $items = [];
        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);
            if (is_dir($appItems[$k])) {
                $subitems = glob($appItems[$k] . DIRECTORY_SEPARATOR . "*.php");
                foreach ($subitems as $sk => $sm) {
                    $sm = str_replace($appItems[$k] . DIRECTORY_SEPARATOR, "", $sm);
                    $sm = str_replace('.php', "", $sm);
                    $subitems[$sk] = [
                        'type' => 'app',
                        'label' => $sm,
                        'icon' => 'fa fa-cube',
                        'class' => "app.models.{$m}." . $sm,
                        'class_path' => 'app.models',
                        'exist' => (@class_exists($sm)) ? 'yes' : 'no',
                        'type' => 'app',
                        'active' => @$_GET['active'] == "app.{$m}." . $sm,
                        'url' => Yii::app()->controller->createUrl('/dev/genModel/index', [
                            'active' => "app.{$m}." . $sm,
                        ]),
                        'target' => 'col2',
                    ];
                }
                
                
                array_unshift($items,[
                    'type' => 'app',
                    'label' => $m,
                    'class' => 'app.models.' . $m,
                    'class_path' => 'app.models',
                    'exist' => (@class_exists($m)) ? 'yes' : 'no',
                    'type' => 'app',
                    'active' => @$_GET['active'] == 'app.' . $m,
                    'target' => 'col2',
                    'items' => $subitems
                ]);
            } else {
                $items[] = [
                    'type' => 'app',
                    'label' => $m,
                    'icon' => 'fa fa-cube',
                    'class' => 'app.models.' . $m,
                    'class_path' => 'app.models',
                    'exist' => (@class_exists($m)) ? 'yes' : 'no',
                    'type' => 'app',
                    'active' => @$_GET['active'] == 'app.' . $m,
                    'url' => Yii::app()->controller->createUrl('/dev/genModel/index', [
                        'active' => 'app.' . $m,
                    ]),
                    'target' => 'col2',
                ];
            }
        }
        $models[] = [
            'type' => 'app',
            'label' => 'App',
            'items' => $items,
        ];
        return $models;
    }

    public static function getFields($modelClass) {
        if (class_exists($modelClass) && method_exists($modelClass, 'model')) {
            return $modelClass::model()->getTableSchema()->getColumnNames();
        }
    }

}
