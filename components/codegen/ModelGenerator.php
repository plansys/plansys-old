<?php

class ModelGenerator extends CodeGenerator {

    
    
    //Helper UI
    public static function listAllFile() {
        $dir = Yii::getPathOfAlias("application.models");
        $appDir = Yii::getPathOfAlias("app.models");

        $devItems = glob($dir . DIRECTORY_SEPARATOR . "*");
        $appItems = glob($appDir . DIRECTORY_SEPARATOR . "*");

        $items = array();
        foreach ($devItems as $k => $m) {
            $m = str_replace($dir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $devItems[$k] = array(
                'name' => $m,
                'class' => 'application.models.' . $m,
                'class_path' => 'application.models',
                'exist' => (class_exists($m)) ? 'yes' : 'no',
                'type' => 'dev'
            );
        }

        foreach ($appItems as $k => $m) {
            $m = str_replace($appDir . DIRECTORY_SEPARATOR, "", $m);
            $m = str_replace('.php', "", $m);

            $appItems[$k] = array(
                'name' => $m,
                'class' => 'app.models.' . $m,
                'class_path' => 'app.models',
                'exist' => (class_exists($m)) ? 'yes' : 'no',
                'type' => 'app'
            );
        }
        $models = array(
            array(
                'name' => 'Plansys',
                'items' => $devItems,
            ),
            array(
                'name' => 'Application',
                'items' => $appItems,
            )
        );
        return $models;
    }

    public static function getModelPath($class, $type) {
        $classPath = Yii::getPathOfAlias($class);
        if ($type == 'dev')
            $basePath = Yii::getPathOfAlias('application');
        else
            $basePath = Yii::getPathOfAlias('app');
        $classPath = str_replace($basePath, '', $classPath);
        $classPath = $classPath . '.php';
        return $classPath;
    }

    protected $baseClass = "ActiveRecord";
    protected $basePath = "application.models";
    protected $model; //model yang akan digenerate (e.g. User)
    protected $modelCode; //class ModelCode dari Gii
    public $modelInfo; //salah satu property dari ModelCode tentang ModelInfo

    protected function getTablePrefix($tableName) {
        $parts = explode("_", $tableName);
        if (strlen($parts[0]) == 1) {
            return $parts[0];
        } else {
            return "";
        }
    }

    protected function getTableModel($tableName) {

        Yii::import('system.gii.CCodeModel');
        $model = new ModelCode();
        $model->loadStickyAttributes();
        $model->tableName = $tableName;
        $model->tablePrefix = $this->getTablePrefix($tableName);
        $model->modelClass = $this->class;

        return $model;
    }

    protected function removeModelProperties() {
        $info = $this->modelInfo;

        $colnames = array_reverse(array_keys($info['columns']));
        foreach ($colnames as $name) {
            $this->removeProperty($name, "protected");
        }
    }

    protected function addModelProperties() {
        $info = $this->modelInfo;

        $colnames = array_reverse(array_keys($info['columns']));
        foreach ($colnames as $name) {
            $this->addProperty($name, "protected");
        }
    }

    protected function removeRelationProperties() {

        ## remove old relation property
        $relationBody = $this->getFunctionBody('relations');
        $isReturning = false;
        foreach ($relationBody as $r) {
            if (preg_match("/\s*return\s*[array|\[]/ix", $r)) {
                $isReturning = true;
                continue;
            }

            if ($isReturning) {
                if (preg_match("/.*=>.*/", $r)) {
                    preg_match('/(?<=^|[\s,])(?:([\'"]).*?\1|[^\s,\'"]+)(?=[\s,]|$)/', $r, $matches);
                    $var = trim(trim($matches[0], "'"), '"');

                    $this->removeProperty($var);
                }

                if (preg_match("/\s*}\s*/", $r)) {
                    break;
                }
            }
        }
    }

    public function addRelations() {
        $info = $this->modelInfo;

        ## add new relation property
        $relations = array();
        $lastIndex = count($info['relations']) - 1;
        $i = 0;
        foreach ($info['relations'] as $k => $v) {
            $relations[] = <<<EOF
            '{$k}' => $v,
EOF;

            $i++;
        }
        $relations = implode("\n", $relations);
        $relationsFunc = <<<EOF
        return array(
{$relations}
        );
EOF;
        $this->updateFunction('relations', $relationsFunc);
    }

    protected function addRules() {
        $info = $this->modelInfo;
        $rules = array();
        foreach ($info['rules'] as $k => $v) {
            $rules[] = <<<EOF
            $v,
EOF;
        }
        $rules = implode("\n", $rules);
        $rulesFunc = <<<EOF
        return array(
{$rules}
        );
EOF;
        $this->updateFunction('rules', $rulesFunc);
    }

    protected function addAttributeLabels() {
        $info = $this->modelInfo;

        $colnames = array_reverse(array_keys($info['columns']));
        $attributeLabels = array();
        foreach ($colnames as $name) {
            $column = <<<EOF
            '{$name}'=> '{$info['labels'][$name]}',
EOF;
            array_unshift($attributeLabels, $column);
        }

        $attributeLabels = implode("\n", $attributeLabels);
        $attributeLabelsFunc = <<<EOF
        return array(
{$attributeLabels}
        );
EOF;
        $this->updateFunction('attributeLabels', $attributeLabelsFunc);
    }

    protected function addTableName($tableName = null) {
        $model = $this->model;
        if ($model == null) {
            $tableNameFunc = <<<EOF
        return '{$tableName}';
EOF;
        } else {
            $tableNameFunc = <<<EOF
        return '{$model->tableName}';
EOF;
        }

        $this->updateFunction('tableName', $tableNameFunc);
    }

    public function generateClass($tableName) {
        $this->modelCode = $this->getTableModel($tableName);
		
        if ($this->modelCode->validate()) {
            $tables = $this->modelCode->prepare();
            $this->modelInfo = $tables[0];

            ## add relation function
            $this->addRelations();

            ## add rules function
            $this->addRules();

            return true;
        } else {
            return false;
        }
    }

    public function __construct($class, $type) {
        ## kode di bawah hanya dijalankan ketika model sudah ada
        if ($type != 'dev') {
            $this->basePath = "app.models";
        }

        if (is_null($this->model) && class_exists($class)) {
            $this->load($class);
            $this->model = new $class;
        } else {
            $this->load($class);
        }

        ## jika class ini sudah ada, maka
        if (!is_null($this->model) && method_exists($this->model, 'tableName')) {
            $tableName = $this->model->tableName();

            ## dapatkan model info untuk $class
            $this->modelCode = $this->getTableModel($tableName);
            if ($this->modelCode->validate()) {
                $tables = $this->modelCode->prepare();
                $this->modelInfo = $tables[0];
            }
        } else {
            ## kalau belum ada, maka:
            $tableName = Helper::camelToUnderscore($class);
            $this->addTableName($tableName);
            $success = $this->generateClass($tableName);

            if (!$success) {
				if (count($this->modelCode->errors) > 0) {
                    file_put_contents($this->filePath, "");
					$errors = $this->modelCode->errors;
					$error = reset($errors);
					
					throw new Exception("Gagal meng-generate Class [{$this->classPath}] dengan nama tabel [{$tableName}].
						{$error[0]}
					");
				} else {
					## note: will never run...
					
					$tryDefaultTable = 'p_' . $tableName;
					$this->addTableName($tryDefaultTable);
					$tryDefault = $this->generateClass($tryDefaultTable);
					
					if (!$tryDefault) {
						file_put_contents($this->filePath, "");
						throw new Exception("Gagal meng-generate Class [{$this->classPath}] dengan nama tabel [{$tableName}].
							Pastikan table tersebut ada di database.");
					}
				}
            }
        }
    }

}
