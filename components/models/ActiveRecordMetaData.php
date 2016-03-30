<?php

class ActiveRecordMetaData extends CActiveRecordMetaData {
    public function __construct($model) {
        $this->_modelClassName = get_class($model);

        $tableName = $model->tableName();
        if (($table = $model->getDbConnection()->getSchema()->getTable($tableName)) === null)
            throw new CDbException(Yii::t('yii', 'The table "{table}" for active record class "{class}" cannot be found in the database.', array('{class}' => $this->_modelClassName, '{table}' => $tableName)));

        if (($modelPk = $model->primaryKey()) !== null || $table->primaryKey === null) {
            $table->primaryKey = $modelPk;
            if (is_string($table->primaryKey) && isset($table->columns[$table->primaryKey]))
                $table->columns[$table->primaryKey]->isPrimaryKey = true;
            elseif (is_array($table->primaryKey)) {
                foreach ($table->primaryKey as $name) {
                    if (isset($table->columns[$name]))
                        $table->columns[$name]->isPrimaryKey = true;
                }
            }
        }
        $this->tableSchema = $table;
        $this->columns = $table->columns;

        foreach ($table->columns as $name => $column) {
            if (!$column->isPrimaryKey && $column->defaultValue !== null)
                $this->attributeDefaults[$name] = $column->defaultValue;
        }

        $driver = $model->getDbConnection()->driverName;
        $rels = ActiveRecord::formatRelationDef($model->relations(), $driver);
        foreach ($rels as $name => $config) {
            $this->addRelation($name, $config);
        }
    }

}
