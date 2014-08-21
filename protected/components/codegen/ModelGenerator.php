<?php

class ModelGenerator extends CodeGenerator {

    protected $baseClass = "ActiveRecord";
    protected $basePath = "application.models";
    protected $model;
    protected $modelInfo;

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

    protected function addRelations() {
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

    protected function addTableName() {
        $model = $this->model;

        $tableNameFunc = <<<EOF
        return '{$model->tableName}';
EOF;
        $this->updateFunction('tableName', $tableNameFunc);
    }

    public function generateClass($tableName) {
        $this->model = $this->getTableModel($tableName);

        if ($this->model->validate()) {
            $tables = $this->model->prepare();
            $this->modelInfo = $tables[0];

            ## add relation function
            $this->addRelations();

            ## add rules function
            $this->addRules();

            ## add tablename function
            $this->addTableName();
            
            ## add columns function
            $this->addAttributeLabels();
        } else {
            var_dump($this->model->errors);
        }
    }

    public static function generate($tableName, $class) {
        $mg = new ModelGenerator();
        $mg->load($class);
        $mg->generateClass($tableName);

        return $mg;
    }

}
