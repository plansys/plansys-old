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
    private $__relationsObj = array();
    private $__oldRelations = array();
    private $__isRelationLoaded = false;
    private $__defaultPageSize = 25;
    private $__pageSize = array();
    private $__page = array();
    private $__relInsert = array();
    private $__relUpdate = array();
    private $__relDelete = array();

    private function initRelation() {
        $static = !(isset($this) && get_class($this) == get_called_class());

        if (!$static && !$this->__isRelationLoaded) {
            $this->loadRelations();
        }
    }

    private function relPagingCriteria($name) {
        $page = @$this->__page[$name] ? $this->__page[$name] : 1;
        $pageSize = $this->{$name . 'PageSize'};
        $start = ($page - 1) * $pageSize;

        return array(
            'limit' => $pageSize,
            'offset' => $start
        );
    }

    public function __call($name, $args) {
        $this->initRelation();

        if (isset($this->__relations[$name])) {
            if (is_numeric($args[0])) {
                $this->__page[$name] = $args[0];
                if (count($args) == 2 && is_numeric($args[1])) {
                    $this->__pageSize[$name] = $args[1];
                }
                $criteria = $this->relPagingCriteria($name);
            } else if (is_array($args[0])) {
                $opt = $args[0];

                if (isset($opt['page'])) {
                    $this->__page[$name] = $opt['page'];
                    unset($opt['page']);
                }

                if (isset($opt['pageSize'])) {
                    $this->__pageSize[$name] = $opt['pageSize'];
                    unset($opt['pageSize']);
                }

                $criteria = $this->relPagingCriteria($name);
                $criteria = array_merge($criteria, $opt);
            }

            $this->loadRelations($name, $criteria);
            $this->applyRelChange($name);
            return $this->$name;
        } else {
            return parent::__call($name, $args);
        }
    }

    public function __set($name, $value) {
        switch (true) {
            case Helper::isLastString($name, 'PageSize'):
                $name = substr_replace($name, '', -8);
                $this->__pageSize[$name] = $value;
                break;
            case Helper::isLastString($name, 'Insert'):
                $this->initRelation();

                $name = substr_replace($name, '', -6);
                if (isset($this->__relations[$name])) {
                    $this->__relInsert[$name] = $value;
                }
                break;
            case Helper::isLastString($name, 'Update'):
                $this->initRelation();
                $name = substr_replace($name, '', -6);

                if (isset($this->__relations[$name])) {
                    $this->__relUpdate[$name] = $value;
                }
                break;
            case Helper::isLastString($name, 'Delete'):
                $this->initRelation();

                $name = substr_replace($name, '', -6);
                if (isset($this->__relations[$name])) {
                    $this->__relDelete[$name] = $value;
                }
                break;
            default:
                parent::__set($name, $value);
                break;
        }
    }

    public function __get($name) {
        switch (true) {
            case Helper::isLastString($name, 'Count'):
                $name = substr_replace($name, '', -5);
                $this->initRelation();
                if (isset($this->__relations[$name])) {
                    $rel = $this->__relations[$name];
                    if (count($rel) == 0) {
                        return 0;
                    } else if (Helper::is_assoc($rel)) {
                        return 1;
                    } else {
                        $c = $this->getRelated($name, true, array(
                            'select' => 'count(1) as id',
                        ));
                        return $c[0]->id;
                    }
                }
                break;
            case Helper::isLastString($name, 'PageSize'):
                $name = substr_replace($name, '', -8);
                if (isset($this->__pageSize[$name])) {
                    return $this->__pageSize[$name];
                } else {
                    return $this->__defaultPageSize;
                }
                break;
            case Helper::isLastString($name, 'CurrentPage'):
                $name = substr_replace($name, '', -11);
                return @$this->__page[$name] ? $this->__page[$name] : 1;
                break;
            case Helper::isLastString($name, 'Insert'):
                $name = substr_replace($name, '', -6);
                return @$this->__relInsert[$name];
                break;
            case Helper::isLastString($name, 'Update'):
                $name = substr_replace($name, '', -6);
                return @$this->__relUpdate[$name];
                break;
            case Helper::isLastString($name, 'Delete'):
                $name = substr_replace($name, '', -6);
                return @$this->__relDelete[$name];
                break;
            case isset($this->getMetaData()->relations[$name]):
                $this->initRelation();
                return @$this->__relations[$name];
                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    public static function toArray($models = array()) {
        $result = array();
        foreach ($models as $k => $m) {
            $result[$k] = $m->attributes;
        }
        return $result;
    }

    public function loadRelations($name = null, $criteria = array()) {
        foreach ($this->getMetaData()->relations as $k => $rel) {
            if (!is_null($name) && $k != $name) {
                continue;
            }

            if (!isset($this->__relations[$k]) || !is_null($name)) {
                if (@class_exists($rel->className)) {
                    switch (get_class($rel)) {
                        case 'CHasOneRelation':
                        case 'CBelongsToRelation':
                            if (is_string($rel->foreignKey)) {
                                $class = $rel->className;
                                $table = $class::tableName();

                                $this->__relationsObj[$k] = $this->getRelated($k, false, $criteria);

                                if (isset($this->__relationsObj[$k])) {
                                    $this->__relations[$k] = $this->__relationsObj[$k]->attributes;

                                    foreach ($this->__relations[$k] as $i => $j) {
                                        if (is_array($this->__relations[$k][$i])) {
                                            unset($this->__relations[$k][$i]);
                                        }
                                    }
                                }
                            }
                            break;
                        case 'CManyManyRelation':
                        case 'CHasManyRelation':
                            //without through
                            if (is_string($rel->foreignKey)) {
                                $this->__relationsObj[$k] = $this->getRelated($k, true, $criteria);

                                if (is_array($this->__relationsObj[$k])) {
                                    $this->__relations[$k] = array();

                                    foreach ($this->__relationsObj[$k] as $i => $j) {
                                        $this->__relations[$k][$i] = $j->getAttributes(true, false);
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

    public function getRelChanges($name) {
        return array(
            'insert' => count(@$this->__relInsert[$name]) > 0 ? @$this->__relInsert[$name] : array(),
            'update' => count(@$this->__relUpdate[$name]) > 0 ? @$this->__relUpdate[$name] : array(),
            'delete' => count(@$this->__relDelete[$name]) > 0 ? @$this->__relDelete[$name] : array(),
        );
    }

    private function applyRelChange($name) {
        if (count(@$this->__relDelete[$name]) > 0) {
            foreach ($this->__relDelete[$name] as $i) {
                foreach ($this->__relations[$name] as $q => $r) {
                    if (@$r['id'] == $i) {
                        array_splice($this->__relations[$name], $q, 1);
                    }
                }
            }
        }

        if (count(@$this->__relInsert[$name]) > 0) {
            foreach ($this->__relInsert[$name] as $k => $i) {
                $this->__relations[$name][] = $i;
            }
        }

        if (count(@$this->__relUpdate[$name]) > 0) {
            foreach ($this->__relUpdate[$name] as $k => $i) {
                foreach ($this->__relations[$name] as $q => $r) {
                    if (isset($i['id']) && isset($r['id']) && $r['id'] == $i['id']) {
                        $this->__relations[$name][$q] = $i;
                    }
                }
            }
        }
    }

    public function setAttributes($values, $safeOnly = false, $withRelation = true) {

        parent::setAttributes($values, $safeOnly);
        $this->initRelation();

        foreach ($this->__relations as $k => $r) {
            if (isset($values[$k])) {
                $rel = $this->getMetaData()->relations[$k];
                $this->__relations[$k] = $values[$k];

                if (is_string($values[$k]) || (is_array($values[$k]))) {
                    if (is_string($values[$k])) {
                        $attr = json_decode($values[$k], true);
                        if (!is_array($attr)) {
                            $attr = array();
                        }
                    } else {
                        $attr = $values[$k];
                    }

                    if (Helper::is_assoc($values[$k])) {
                        switch (get_class($rel)) {
                            case 'CHasOneRelation':
                            case 'CBelongsToRelation':
                                foreach ($attr as $i => $j) {
                                    if (is_array($j)) {
                                        unset($attr[$i]);
                                    }
                                }

                                $relArr = $this->$k;
                                if (is_object($relArr)) {
                                    $relArr->setAttributes($attr, false, false);
                                }
                                $this->__relations[$k] = $attr;
                                break;
                        }
                    }
                }
            }

            if (isset($values[$k . 'Insert'])) {
                $value = $values[$k . 'Insert'];
                $value = is_string($value) ? json_decode($value, true) : $value;
                $this->__relInsert[$k] = $value;
            }

            if (isset($values[$k . 'Update'])) {

                $value = $values[$k . 'Update'];
                $value = is_string($value) ? json_decode($value, true) : $value;
                $this->__relUpdate[$k] = $value;
            }

            if (isset($values[$k . 'Delete'])) {
                $value = $values[$k . 'Delete'];
                $value = is_string($value) ? json_decode($value, true) : $value;
                $this->__relDelete[$k] = $value;
            }

            $this->applyRelChange($k);
        }

        foreach ($this->attributeProperties as $k => $r) {
            if (isset($values[$k])) {
                $this->$k = $values[$k];
            }
        }
    }

    public function getAttributes($names = true, $withRelation = true) {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($this->attributeProperties, $attributes);

        if ($withRelation) {
            $this->initRelation();
            foreach ($this->__relations as $k => $r) {
                $attributes[$k] = $this->__relations[$k];
            }
        }

        return $attributes;
    }

    public function getAttributesRelated($names = true) {
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
            if (is_array($new) && is_array($old)) {
                $rel = $this->getMetaData()->relations[$k];

                switch (get_class($rel)) {
                    case 'CHasOneRelation':
                    case 'CBelongsToRelation':

                        if (count(array_diff_assoc($new, $old)) > 0) {
                            //todo..
                            $class = $rel->className;
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

                            $class = $rel->className;
                            if (isset($this->__relInsert[$k])) {
                                foreach ($this->__relInsert[$k] as $n => $m) {
                                    $this->__relInsert[$k][$n][$rel->foreignKey] = $this->id;
                                }

                                ActiveRecord::batchInsert($class, $this->__relInsert[$k]);
                                $this->__relInsert[$k] = array();
                            }

                            if (isset($this->__relUpdate[$k])) {
                                foreach ($this->__relUpdate[$k] as $n => $m) {
                                    $this->__relUpdate[$k][$n][$rel->foreignKey] = $this->id;
                                }
                                ActiveRecord::batchUpdate($class, $this->__relUpdate[$k]);
                                $this->__relUpdate[$k] = array();
                            }

                            if (isset($this->__relDelete[$k])) {
                                ActiveRecord::batchDelete($class, $this->__relDelete[$k]);
                                $this->__relDelete[$k] = array();
                            }
                        }
                        //with through
                        //todo..
                        break;
                }
            }
            $this->__relations[$k] = $new;
        }
//        var_dump($this->__relations);

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

        if (is_array($data[0])) {
            $ids = array();
            foreach ($data as $i => $j) {
                $ids[] = $j['id'];
            }
        } else {
            $ids = $data;
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
