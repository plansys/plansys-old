<?php

class ActiveRecord extends CActiveRecord {

    /**
     * @return array of used behaviors
     */
    public function behaviors() {
        return array(
            'LoggableBehavior' => array(
                'class' => 'LoggableBehavior'
            ),
        );
    }

    private $__relations = array();
    private $__oldRelations = array();

    public function loadRelations() {
        foreach ($this->getMetaData()->relations as $k => $r) {
            if (!isset($this->__relations[$k])) {
                if (@class_exists($r->className)) {

                    $this->__relations[$k] = $this->$k;
                    foreach ($this->__relations[$k] as $i => $j) {
                        $this->__relations[$k][$i] = $j->attributes;
                    }
                }
            }
        }

        $this->__oldRelations = $this->__relations;
    }

    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);

        foreach ($this->__relations as $k => $r) {
            if (isset($values[$k])) {
                $this->__relations[$k] = $values[$k];
            }
        }
    }

    public function getAttributes($names = true) {
        $attributes = parent::getAttributes($names);
        foreach ($this->__relations as $k => $r) {
            $attributes[$k] = json_encode($this->__relations[$k]);
        }

        return $attributes;
    }

    public function getAttributesRelated($names = true) {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($attributes, $this->__relations);

        return $attributes;
    }

    public function getAttributesList($names = true) {
        $attributes = array(
            'Fields' => array_keys(parent::getAttributes($names)),
            'Relations' => array()
        );
        foreach ($this->getMetaData()->relations as $k => $r) {
            if (!isset($attributes['Fields'][$k])) {
                if (@class_exists($r->className)) {
                    $attributes['Relations'][$k] = $k;
                }
            }
        }
        return $attributes;
    }

    public function save($runValidation = true, $attributes = null) {
        $validate = parent::save($runValidation, $attributes);

        if ($validate) {
            foreach ($this->__relations as $k => $new) {
                $new = $new == '' ? array() : $new;
                $old = $this->__oldRelations[$k];

                if (count($old) > 0 || count($new) > 0) {
                    $rel = $this->getMetaData()->relations[$k];

                    switch (get_class($rel)) {
                        case 'CHasOneRelation':
                        case 'CBelongsToRelation':
                            if (count(array_diff_assoc($new, $old)) > 0) {
                                
                            }
                            break;
                        case 'CManyManyRelation':
                        case 'CHasManyRelation':
                            ActiveRecord::batch($rel->className, $new, $old);

                            break;
                    }
                }
            }
        }
    }

    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
     */
    public static function model($className = null) {
        if (is_null($className)) {
            $className = get_called_class();
        }
        return parent::model($className);
    }

    public function getModelFieldList() {
        $fields = array_keys($this->attributes);

        foreach ($fields as $k => $f) {
            if ($this->tableSchema->primaryKey == $f) {
                $type = "HiddenField";
            } else {
                $type = "TextField";
            }

            $array[] = array(
                'name' => $f,
                'type' => $type,
                'label' => $this->getAttributeLabel($f)
            );
        }
        return $array;
    }

    public static function batch($model, $new, $old = array()) {
        
    }

    public static function batchUpdate($model, $data) {
        $table = $model::model()->tableSchema->name;
        $field = $model::model()->tableSchema->columns;
        unset($field['id']);

        $columnCount = count($field);
        $columnName = array_keys($field);
        $update = "";
        foreach ($data as $d) {
            $cond = $d['id'];
            unset($d['id']);
            $update .= "UPDATE {$table} SET ";
            for ($i = 0; $i < $columnCount; $i++) {
                $update .= $columnName[$i] . " = '{$d[$columnName[$i]]}'";
                if ($i !== ($columnCount - 1))
                    $update .= ' , ';
            }
            $update .= " WHERE id='{$cond}';";
        }
        $command = Yii::app()->db->createCommand($update);
        $command->execute();
    }

    public static function batchInsert($model, $data) {
        $table = $model::model()->tableSchema->name;
        $builder = Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand($table, $data);
        $command->execute();
    }

    public function getDefaultFields() {
        $array = $this->modelFieldList;
        $length = count($array);
        $column1 = array();
        $column2 = array();
        $array_id = null;

        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == 'id') {
                $array_id = $array[$k];
                continue;
            }

            if ($k < $length / 2) {
                $column1[] = $array[$k];
            } else {
                $column2[] = $array[$k];
            }
        }

        $column1[] = '<column-placeholder></column-placeholder>';
        $column2[] = '<column-placeholder></column-placeholder>';

        $return = array();
        $return[] = array(
            'type' => 'ActionBar',
        );

        if (!is_null($array_id)) {
            $return[] = $array_id;
        }

        $return[] = array(
            'type' => 'ColumnField',
            'column1' => $column1,
            'column2' => $column2
            )
        ;
        return $return;
    }

}
