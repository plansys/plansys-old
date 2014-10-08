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
    private $__isRelationLoaded = false;
    
    public static function toArray($models = array()) {
        $result = array();
        foreach ($models as $k => $m) {
            $result[$k] = $m->attributes;
        }
        return $result;
    }

    public function loadRelations() {
        foreach ($this->getMetaData()->relations as $k => $rel) {
            if (!isset($this->__relations[$k])) {
                if (@class_exists($rel->className)) {
                    switch (get_class($rel)) {
                        case 'CHasOneRelation':
                        case 'CBelongsToRelation':
                            //todo..
                            if (is_string($rel->foreignKey)) {
                                $class = $rel->className;
                                $table = $class::tableName();
                                $foreignKey = $rel->foreignKey;
                                if (!is_null($this->$foreignKey) && $this->$foreignKey != '') {
                                    $sql = "select * from {$table} where id = {$this->$foreignKey}";

                                    $query = Yii::app()->db->createCommand($sql)->queryRow();
                                    $this->__relations[$k] = $query;
                                } else {
                                    $this->__relations[$k] = array();
                                }
                            }
                            break;
                        case 'CManyManyRelation':
                        case 'CHasManyRelation':
                            //without through
                            if (is_string($rel->foreignKey)) {
                                $this->__relations[$k] = $this->getRelated($k);
                                if (is_array($this->__relations[$k])) {
                                    foreach ($this->__relations[$k] as $i => $j) {
                                        $this->__relations[$k][$i] = $j->attributes;
                                    }
                                }
                            }

                            //with through
                            //todo..
                            break;
                    }
                }
            }
        }
        $this->__isRelationLoaded = true;
        $this->__oldRelations = $this->__relations;
    }

    public function setAttributes($values, $safeOnly = false, $withRelation = true) {
        parent::setAttributes($values, $safeOnly);
        if (!$this->__isRelationLoaded) {
            $this->loadRelations();
        }


        foreach ($this->__relations as $k => $r) {

            if (isset($values[$k])) {
                $rel = $this->getMetaData()->relations[$k];
                $this->__relations[$k] = $values[$k];
                $relArr = $this->$k;

                if (is_string($values[$k])) {
                    $attr = json_decode($values[$k], true);

                    if (!is_array($attr)) {
                        $attr = array();
                    }

                    switch (get_class($rel)) {
                        case 'CHasOneRelation':
                        case 'CBelongsToRelation':
                            foreach ($attr as $i => $j) {
                                if (is_array($j)) {
                                    unset($attr[$i]);
                                }
                            }
                            break;
                    }

                    if (is_object($relArr)) {
                        $relArr->setAttributes($attr, false, false);
                    }
                    $this->__relations[$k] = $attr;
                } elseif (is_array($values[$k])) {
                    if (Helper::is_assoc($values[$k])) {
                        $attr = $values[$k];

                        switch (get_class($rel)) {
                            case 'CHasOneRelation':
                            case 'CBelongsToRelation':
                                foreach ($attr as $i => $j) {
                                    if (is_array($j)) {
                                        unset($attr[$i]);
                                    }
                                }
                                break;
                        }

                        if (is_object($relArr)) {
                            $relArr->setAttributes($attr, false, false);
                        }
                        $this->__relations[$k] = $attr;
                    } else {
                        foreach ($relArr as $i => $j) {
                            foreach ($values[$k] as $v) {
                                if (isset($v['id']) && $j->id == $v['id']) {
                                    $attr = $j->attributes;
                                    foreach ($attr as $x => $y) {
                                        if (!isset($v[$x]))
                                            continue;

                                        if (is_array($y) && is_string($v[$x])) {
                                            $attr[$x] = json_decode($v[$x], true);
                                        } else {
                                            $attr[$x] = $v[$x];
                                        }
                                    }

                                    if (is_object($relArr[$i])) {
                                        $relArr[$i]->setAttributes($attr, false, false);
                                    }
                                    $this->__relations[$k][$i] = $attr;
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($this->attributeProperties as $k => $r) {
            if (isset($values[$k])) {
                $this->$k = $values[$k];
            }
        }
    }

    public function getAttributes($names = true, $withRelation = true) {
        if ($withRelation && !$this->__isRelationLoaded) {
            $this->loadRelations();
        }
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($this->attributeProperties, $attributes);
        if ($withRelation) {
            foreach ($this->__relations as $k => $r) {
                $attributes[$k] = $this->__relations[$k];
            }
        }

        return $attributes;
    }

    public function getAttributesRelated($names = true) {
        if (!$this->__isRelationLoaded) {
            $this->loadRelations();
        }
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($attributes, $this->__relations);
        $attributes = array_merge($this->attributeProperties, $attributes);

        return $attributes;
    }

    public function getAttributeProperties() {
        $props = array();
        $class = new ReflectionClass($this);
        $properties = Helper::getClassProperties($this);

        foreach ($properties as $p) {
            $props[$p->name] = $this->{$p->name};
        }
        return $props;
    }

    public function getAttributesList($names = true) {
        if (!$this->__isRelationLoaded) {
            $this->loadRelations();
        }
        $fields = array();
        $props = array();
        $relations = array();
        foreach (parent::getAttributes($names) as $k => $i) {
            $fields[$k] = $k;
        }
        foreach ($this->getMetaData()->relations as $k => $r) {
            if (!isset($fields[$k])) {
                if (@class_exists($r->className)) {
                    $relations[$k] = $k;
                }
            }
        }
        foreach ($this->attributeProperties as $k => $r) {
            $props[$k] = $k;
        }

        $attributes = array('DB Fields' => $fields);

        if (count($props) > 0) {
            $attributes = $attributes + array('Properties' => $props);
        }

        if (count($relations) > 0) {
            $attributes = $attributes + array('Relations' => $relations);
        }

        return $attributes;
    }

    public function beforeSave() {
        if ($this->primaryKey == '') {
            $table = $this->getMetaData()->tableSchema;
            $primaryKey = $table->primaryKey;
            $this->$primaryKey = null;
        }

        return true;
    }

    public function afterSave() {
        if ($this->isNewRecord) {
            $this->id = Yii::app()->db->getLastInsertID(); // this is hack
        }
        foreach ($this->__relations as $k => $new) {
            $new = $new == '' ? array() : $new;
            $old = $this->__oldRelations[$k];

            if (is_array($new) && is_array($old) && (count($old) > 0 || count($new) > 0)) {
                $rel = $this->getMetaData()->relations[$k];

                switch (get_class($rel)) {
                    case 'CHasOneRelation':
                    case 'CBelongsToRelation':
                        if (count(array_diff_assoc($new, $old)) > 0) {
                            //todo..
                            $class = $rel->class;
                            $model = $class::model()->findByPk($this->{$rel->foreignKey});
                            if (is_null($model)) {
                                $model = new $class;
                            }
                            $model->attributes = $new;
                            $model->{$rel->foreignKey} = $this->id;
                            $model->save();
                        }
                        break;
                    case 'CManyManyRelation':
                    case 'CHasManyRelation':
                        //without through
                        if (is_string($rel->foreignKey)) {
                            $originalNew = $new;
                            foreach ($new as $i => $j) {
                                $new[$i][$rel->foreignKey] = $this->id;
                                $originalNew[$i][$rel->foreignKey] = $this->id;
                                foreach ($new[$i] as $m => $n) {
                                    if (is_array($n)) {
                                        $originalNew[$i][$m] = $n;
                                        $new[$i][$m] = json_encode($n);
                                    }
                                }
                            }
                            foreach ($old as $i => $j) {
                                foreach ($old[$i] as $m => $n) {
                                    if (is_array($n)) {
                                        $old[$i][$m] = json_encode($n);
                                    }
                                }
                            }
                            $new = ActiveRecord::batch($rel->className, $new, $old);


                            foreach ($new as $key => $n) {
                                $originalNew[$key]['id'] = $n['id'];
                            }
                            $new = $originalNew;
                        }
                        //with through
                        //todo..
                        break;
                }
            }
            $this->__relations[$k] = $new;
        }

        return true;
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
        $fields = array_keys(parent::getAttributes());

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

    public static function batch($model, $new, $old = array(), $delete = true) {
        $deleteArr = array();
        $updateArr = array();

        foreach ($old as $k => $v) {
            $is_deleted = true;
            $is_updated = false;

            foreach ($new as $i => $j) {
                if (@$j['id'] == @$v['id']) {
                    $is_deleted = false;
                    if (count(array_diff_assoc($j, $v)) > 0) {
                        $is_updated = true;
                        $updateArr[] = $j;
                    }
                }
            }

            if ($is_deleted) {
                $deleteArr[] = $v;
            }
        }

        $insertArr = array();
        $insertIds = array();
        foreach ($new as $i => $j) {
            if (@$j['id'] == '' || is_null(@$j['id'])) {
                $insertArr[] = $j;
                $insertIds[] = $i;
            } else if (count($old) == 0) {
                $updateArr[] = $j;
            }
        }

        if (count($insertArr) > 0) {
            ActiveRecord::batchInsert($model, $insertArr);
        }

        if (count($updateArr) > 0) {
            ActiveRecord::batchUpdate($model, $updateArr);
        }

        if ($delete && count($deleteArr) > 0) {
            ActiveRecord::batchDelete($model, $deleteArr);
        }

        return array_merge($insertArr, $updateArr);
    }

    public static function batchDelete($model, $data) {
        if (!is_array($data) || count($data) == 0)
            return;

        $table = $model::model()->tableSchema->name;

        $ids = array();
        foreach ($data as $i => $j) {
            $ids[] = $j['id'];
        }
        $delete = "DELETE FROM {$table} WHERE id IN (" . implode(",", $ids) . ");";

        $command = Yii::app()->db->createCommand($delete);
        $command->execute();
    }

    public static function batchUpdate($model, $data) {
        if (!is_array($data) || count($data) == 0)
            return;
        $table = $model::model()->tableSchema->name;
        $field = $model::model()->tableSchema->columns;
        unset($field['id']);

        $columnCount = count($field);
        $columnName = array_keys($field);
        $update = "";
        foreach ($data as $d) {
            $cond = $d['id'];
            unset($d['id']);
            $updatearr = array();
            for ($i = 0; $i < $columnCount; $i++) {
                if (isset($columnName[$i]) && isset($d[$columnName[$i]])) {
                    $updatearr[] = $columnName[$i] . " = '{$d[$columnName[$i]]}'";
                }
            }

            $updatesql = implode(",", $updatearr);
            if ($updatesql != '') {
                $update .= "UPDATE {$table} SET {$updatesql} WHERE id='{$cond}';";
            }
        }
        if ($update != '') {
            $command = Yii::app()->db->createCommand($update);
            $command->execute();
        }
    }

    public static function listData($idField, $valueField, $condition = '') {
        $class = get_called_class();
        return CHtml::listData($class::model()->findAll(), $idField, $valueField);
    }

    public static function batchInsert($model, &$data) {
        if (!is_array($data) || count($data) == 0)
            return;

        $table = $model::model()->tableSchema->name;
        $builder = Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand($table, $data);
        $command->execute();

        $id = Yii::app()->db->getLastInsertID();
        foreach ($data as &$d) {
            $d['id'] = $id;
            $id++;
        }
    }

    public function getDefaultFields() {
        $array = $this->modelFieldList;
        $length = count($array);
        $column1 = array();
        $column2 = array();
        $array_id = null;

        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == 'id') {
                $array_id = $array [$k];
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
