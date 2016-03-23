<?php

Yii::import("application.framework.gii.*");
Yii::import("application.framework.gii.generators.model.ModelCode");

class ModelGeneratorCode extends ModelCode {
    public $options = [];

    public function prepare() {
        if (($pos = strrpos($this->tableName, '.')) !== false) {
            $schema    = substr($this->tableName, 0, $pos);
            $tableName = substr($this->tableName, $pos + 1);
        } else {
            $schema    = '';
            $tableName = $this->tableName;
        }
        if ($tableName[strlen($tableName) - 1] === '*') {
            $tables = Yii::app()->{$this->connectionId}->schema->getTables($schema);
            if ($this->tablePrefix != '') {
                foreach ($tables as $i => $table) {
                    if (strpos($table->name, $this->tablePrefix) !== 0)
                        unset($tables[$i]);
                }
            }
        } else
            $tables = array($this->getTableSchema($this->tableName));

        $this->files     = array();
        $templatePath    = Yii::getPathOfAlias('application.components.codegen.templates');
        $this->relations = $this->generateRelations();

        foreach ($tables as $table) {
            $tableName = $this->removePrefix($table->name);
            $className = $this->generateClassName($table->name);
            $params    = array(
                'tableName' => $schema === '' ? $tableName : $schema . '.' . $tableName,
                'modelClass' => $className,
                'options' => $this->options,
                'columns' => $table->columns,
                'labels' => $this->generateLabels($table),
                'rules' => $this->generateRules($table),
                'relations' => isset($this->relations[$className]) ? $this->relations[$className] : array(),
                'connectionId' => $this->connectionId,
            );
            
            $filePath = Yii::getPathOfAlias($this->modelPath) . '/' . $className . '.php';
            
            if (is_file($filePath)) {
                unlink($filePath);
            }

            $this->files[] = new CCodeFile(
                $filePath, $this->render($templatePath . '/TplModel.php', $params)
            );
        }
    }
}