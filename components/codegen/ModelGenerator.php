<?php

class ModelGenerator extends CodeGenerator {

    public $extendsFrom;
    public $tableName;

    public static function getRuleList() {
        return [
            'required'  => 'Required',
            'email'     => 'Email',
            'boolean'   => 'Boolean',
            'compare'   => 'Compare',
            'date'      => 'Date',
            'default'   => 'Default',
            'exist'     => 'Exist',
            'file'      => 'File',
            'in'        => 'Range',
            'length'    => 'Length',
            'numerical' => 'Number',
            'match'     => 'Regex Match',
            'unique'    => 'Unique',
            'url'       => 'Url',
            '---1'      => '---',
            'safe'      => 'Safe',
            'unsafe'    => 'Unsafe',
            '---'       => '---',
            'custom'    => 'Custom'
        ];
    }

    public static function init($classAlias, $mode = 'load') {
        $path = explode('.', $classAlias);
        $class = ucfirst(array_pop($path));
        $path[count($path) - 1] = lcfirst($path[count($path) - 1]);

        $m = new ModelGenerator;
        $m->basePath = implode(".", $path);

        if (Helper::isValidVar($class)) {
            $m->load($class);

            $m->extendsFrom = 'ActiveRecord';
            if ($mode == 'create') {
                $m->generateTableName();
            } else {
                $model = $class::model();
                if (method_exists($model, 'tableName')) {
                    $m->tableName = $model->tableName();
                }
            }
            return $m;
        } else {
            return null;
        }
    }

    public function generateTableName() {
        $tableNameFunc = <<<EOF
        return '{$this->tableName}';
EOF;
        $this->updateFunction('tableName', $tableNameFunc);
    }

    public function getRelations() {
        $relations = [];
        return $relations;
    }

    public function getModel() {
        $class = $this->class;

        if (method_exists($class, 'model')) {
            return $class::model();
        } else {
            return null;
        }
    }

    public function getRules() {
        $rules = [];

        if (method_exists($this->model, 'rules')) {
            $rulesRaw = $this->model->rules();
            foreach ($rulesRaw as $r) {
                $fields = array_shift($r);
                $rule = array_shift($r);

                $rules[] = [
                    'fields'  => $fields,
                    'rule'    => $rule,
                    'options' => $r
                ];
            }
        }

        return $rules;
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
                    'type'       => 'plansys',
                    'label'      => $m,
                    'icon'       => 'fa fa-cube',
                    'class'      => 'application.models.' . $m,
                    'class_path' => 'application.models',
                    'exist'      => (class_exists($m)) ? 'yes' : 'no',
                    'type'       => 'dev',
                    'active'     => @$_GET['active'] == 'plansys.' . $m,
                    'url'        => Yii::app()->controller->createUrl('/dev/genModel/index', [
                        'active' => 'plansys.' . $m,
                    ]),
                    'target'     => 'col2',
                ];
            }

            $models[] = [
                'type'  => 'plansys',
                'label' => 'Plansys',
                'items' => $devItems,
            ];
        }

        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $appItems[$k] = [
                'type'       => 'app',
                'label'      => $m,
                'icon'       => 'fa fa-cube',
                'class'      => 'app.models.' . $m,
                'class_path' => 'app.models',
                'exist'      => (class_exists($m)) ? 'yes' : 'no',
                'type'       => 'app',
                'active'     => @$_GET['active'] == 'app.' . $m,
                'url'        => Yii::app()->controller->createUrl('/dev/genModel/index', [
                    'active' => 'app.' . $m,
                ]),
                'target'     => 'col2',
            ];
        }
        $models[] = [
            'type'  => 'app',
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
