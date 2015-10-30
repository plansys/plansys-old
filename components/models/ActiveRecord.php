<?php

class ActiveRecord extends CActiveRecord {
    const DEFAULT_PAGE_SIZE = 25;
    protected $_softDelete        = [];
    private   $__relations        = [];
    private   $__relationsObj     = [];
    private   $__isRelationLoaded = false;
    private   $__pageSize         = [];
    private   $__page             = [];
    private   $__relInsert        = [];
    private   $__relUpdate        = [];
    private   $__relDelete        = [];
    private   $__relReset         = [];
    private   $__tempVar          = [];

    public static function execute($sql, $params = []) {
        return Yii::app()->db->createCommand($sql)->execute($params);
    }

    public static function queryScalar($sql, $params = []) {
        return Yii::app()->db->createCommand($sql)->queryScalar($params);
    }

    public static function queryColumn($sql, $params = []) {
        return Yii::app()->db->createCommand($sql)->queryColumn($params);
    }

    public static function jsonToArray(&$post, $key, $shouldReturn = false, $flattenPost = false) {
        $new = [];

        if (isset($post[$key . 'Insert']) && is_string($post[$key . 'Insert']))
            $new[$key . 'Insert'] = json_decode($post[$key . 'Insert'], true);

        if (isset($post[$key . 'Update']) && is_string($post[$key . 'Update']))
            $new[$key . 'Update'] = json_decode($post[$key . 'Update'], true);

        if (isset($post[$key . 'Delete']) && is_string($post[$key . 'Delete']))
            $new[$key . 'Delete'] = json_decode($post[$key . 'Delete'], true);

        if ($shouldReturn) {
            return $flattenPost ? ActiveRecord::flattenPost($new, $key, true) : $new;
        } else {
            if (isset($new[$key . 'Update'])) {
                $post[$key . 'Update'] = $new[$key . 'Update'];
            }
            if (isset($new[$key . 'Delete'])) {
                $post[$key . 'Delete'] = $new[$key . 'Delete'];
            }
            if (isset($new[$key . 'Insert'])) {
                $post[$key . 'Insert'] = $new[$key . 'Insert'];
            }
            if ($flattenPost) {
                ActiveRecord::flattenPost($post, $key);
            }
        }
    }

    public static function flattenPost(&$post, $key, $shouldReturn = false) {
        $new = [];
        if (is_array($post[$key . 'Insert']) && is_array($post[$key . 'Update'])) {
            $new = array_merge($post[$key . 'Insert'], $post[$key . 'Update']);
        } else if (is_array($post[$key . 'Insert'])) {
            $new = $post[$key . 'Insert'];
        } else if (is_array($post[$key . 'Update'])) {
            $new = $post[$key . 'Update'];
        }

        if (is_array($post[$key . 'Delete'])) {
            foreach ($new as $k => $n) {
                if (isset($n['id']) && in_array($n['id'], $post[$key . 'Delete'])) {
                    unset($new[$k]);
                }
            }
        }

        if ($shouldReturn) {
            return $new;
        } else {
            if (is_array($post[$key . 'Insert'])) {
                unset($post[$key . 'Insert']);
            }
            if (is_array($post[$key . 'Update'])) {
                unset($post[$key . 'Update']);
            }
            if (is_array($post[$key . 'Delete'])) {
                unset($post[$key . 'Delete']);
            }
            $post[$key] = $new;
        }
    }

    public static function toArray($models = []) {
        $result = [];
        foreach ($models as $k => $m) {
            $result[$k] = $m->attributes;
        }
        return $result;
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

    public static function batchPost($model, $post, $name, $attr = []) {
        if (is_object($model) && is_subclass_of($model, 'ActiveRecord')) {
            $model->attributes = [
                'currentModelInsert' => $post[$name . 'Insert'],
                'currentModelUpdate' => $post[$name . 'Update'],
                'currentModelDelete' => $post[$name . 'Delete'],
            ];
            if ($model->validate()) {
                $model->saveRelation();
                return $model;
            } else {
                return $model;
            }
        } elseif (is_string($model)) {
            $cols = array_keys($attr);
    
            ## insert
            if (isset($post[$name . 'Insert']) && is_string($post[$name . 'Insert'])) {
                $post[$name . 'Insert'] = json_decode($post[$name . 'Insert'], true);
            }
            if (count(@$post[$name . 'Insert']) > 0) {
                if (count($attr) > 0) {
                    foreach ($post[$name . 'Insert'] as $k => $i) {
                        if (isset($i['id']) && is_numeric($i['id'])) {
                            if (!is_array($post[$name . 'Update'])) {
                                $post[$name . 'Update'] = [];
                            }
                            $post[$name . 'Update'][] = $i;
                            unset($post[$name . 'Insert'][$k]);
                            continue;
                        }
    
                        foreach ($attr as $a => $b) {
                            if (isset($post[$name . 'Insert'][$k])) {
                                $post[$name . 'Insert'][$k][$a] = $b;
                            }
                        }
                    }
                } else {
                    foreach ($post[$name . 'Insert'] as $k => $i) {
                        if (isset($i['id']) && is_numeric($i['id'])) {
                            if (!is_array($post[$name . 'Update'])) {
                                $post[$name . 'Update'] = [];
                            }
                            $post[$name . 'Update'][] = $i;
                            unset($post[$name . 'Insert'][$k]);
                            continue;
                        }
                    }
                }
                ActiveRecord::batchInsert($model, $post[$name . 'Insert']);
            }
    
            ## update
            if (isset($post[$name . 'Update']) && is_string($post[$name . 'Update'])) {
                $post[$name . 'Update'] = json_decode($post[$name . 'Update'], true);
            }
            if (count(@$post[$name . 'Update']) > 0) {
                if (count($attr) > 0) {
                    foreach ($post[$name . 'Update'] as $k => $i) {
                        foreach ($attr as $a => $b) {
                            if (isset($post[$name . 'Update'][$k])) {
                                $post[$name . 'Update'][$k][$a] = $b;
                            }
                        }
                    }
                }
    
                ActiveRecord::batchUpdate($model, $post[$name . 'Update']);
            }
            
            ## delete
            if (isset($post[$name . 'Delete']) && is_string($post[$name . 'Delete'])) {
                $post[$name . 'Delete'] = json_decode($post[$name . 'Delete'], true);
            }
            if (count(@$post[$name . 'Delete']) > 0) {
                ActiveRecord::batchDelete($model, $post[$name . 'Delete']);
            }
        }
    }

    public static function batchInsert($modelClass, &$data, $assignNewID = true) {
        if (!is_array($data) || count($data) == 0)
            return;

        $model = null;
        if (!class_exists($modelClass)) {
            $table = $modelClass;
        } else {
            $model = $modelClass::model();
            $table = $model->tableSchema->name;
        }

        $builder = Yii::app()->db->schema->commandBuilder;
        $command = $builder->createMultipleInsertCommand($table, $data);
        $command->execute();

        if ($assignNewID && !!$model) {
            $id = Yii::app()->db->getLastInsertID();
            $pk = $model->tableSchema->primaryKey;
            foreach ($data as &$d) {
                $d[$pk] = $id;
                $id++;
            }
        }
    }

    public static function batchUpdate($model, $data) {
        if (!is_array($data) || count($data) == 0)
            return;
        $pk    = $model::model()->tableSchema->primaryKey;
        $table = $model::model()->tableSchema->name;
        $field = $model::model()->tableSchema->columns;
        unset($field['id']);

        $columnCount = count($field);
        $columnName  = array_keys($field);
        $update      = "";

        $rels        = $model::model()->relations();
        $foreignKeys = [];
        foreach ($rels as $r) {
            if ($r[0] == 'CBelongsToRelation' && is_string($r[2])) {
                $foreignKeys[] = $r[2];
            }
        }

        foreach ($data as $d) {
            $cond = $d[$pk];
            unset($d[$pk]);
            $updatearr = [];
            for ($i = 0; $i < $columnCount; $i++) {

                ## cek apakah ada kolom yg dimaksud, jika ada maka
                if (isset($columnName[$i]) && array_key_exists($columnName[$i], $d)) {

                    ## jika yg kolom itu foreign key DAN kolom nya kosong, maka set NULL (karena foreign_key ga boleh string kosong)
                    if (in_array($columnName[$i], $foreignKeys) && $d[$columnName[$i]] == '') {
                        $updatearr[] = '`' . $columnName[$i] . "` = NULL";
                    } else {
                        ## selain itu, hajar seperti biasa...
                        if (is_null($d[$columnName[$i]])) {
                            $updatearr[] = '`' . $columnName[$i] . "` = NULL";
                        } else {
                            $updatearr[] = '`' . $columnName[$i] . "` = '{$d[$columnName[$i]]}'";
                        }
                    }
                }
            }

            $updatesql = implode(",", $updatearr);
            if ($updatesql != '') {
                $update .= "UPDATE {$table} SET {$updatesql} WHERE {$pk}='{$cond}';";
            }
        }
        if ($update != '') {
            $command = Yii::app()->db->createCommand($update);
            try {
                $command->execute();
            } catch (Exception $e) {
                var_dump($data, $e);
                die();
            }
        }
    }

    public static function batchDelete($model, $data, $options = []) {
        if (!is_array($data) || count($data) == 0)
            return;

        $instance = null;
        if (isset($options['table'])) {
            $table = $options['table'];
        } else {
            $instance = $model::model();
            $table    = $instance->tableSchema->name;
        }

        if (isset($options['pk'])) {
            $pk = $options['pk'];
        } else {
            $pk = $instance->tableSchema->primaryKey;
        }

        if (is_array($data[0])) {
            $ids = [];
            foreach ($data as $i => $j) {
                if (is_numeric(@$j[$pk])) {
                    $ids[] = $j[$pk];
                }
            }
        } else {
            $ids = array_filter($data);
        }

        if (!empty($ids)) {
            $idsString = implode(",", $ids);
            $condition = isset($options['condition']) ? $options['condition'] : "{$pk} IN (:ids)";
            $condition = str_replace(":ids", $idsString, $condition);

            if (empty($instance->_softDelete)) {
                $sql     = "DELETE FROM {$table} WHERE $condition;";
                $command = Yii::app()->db->createCommand($sql);
                try {
                    $command->execute();
                } catch (CDbException $e) {
                    if (!isset($options['integrityError']) || (!!@$options['integrityError'])) {
                        if ($e->errorInfo[0] == "23000") {
                            Yii::app()->controller->redirect(["/site/error&id=integrity&msg=" . $e->errorInfo[2]]);
                        }
                    }
                }
            } else {
                $params = [];
                foreach ($ids as $id) {
                    $params[] = [
                        $pk => $id,
                        $instance->_softDelete['column'] => $instance->_softDelete['value']
                    ];
                }
                ActiveRecord::batchUpdate($model, $params);
            }

        }
    }

    public static function batch($model, $new, $old = [], $delete = true) {
        $deleteArr = [];
        $updateArr = [];

        $pk = $model::model()->tableSchema->primaryKey;

        foreach ($old as $k => $v) {
            $is_deleted = true;
            $is_updated = false;

            foreach ($new as $i => $j) {
                if (@$j[$pk] == @$v[$pk]) {
                    $is_deleted = false;
                    if (count(array_diff_assoc($j, $v)) > 0) {
                        $is_updated  = true;
                        $updateArr[] = $j;
                    }
                }
            }

            if ($is_deleted) {
                $deleteArr[] = $v;
            }
        }

        $insertArr = [];
        $insertIds = [];
        foreach ($new as $i => $j) {
            if (@$j[$pk] == '' || is_null(@$j[$pk])) {
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

    public static function listTables() {
        $connection = Yii::app()->db;
        $dbSchema   = $connection->schema;
        $tables     = $dbSchema->getTables();
        return array_keys($tables);
    }

    public static function listData($idField, $valueField, $criteria = []) {

        if (is_bool($criteria)) {
            $criteria = [
                'distinct' => $criteria
            ];
        }

        $class = get_called_class();
        return CHtml::listData($class::model()->findAll($criteria), $idField, $valueField);
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

                $setRelPaging = false;
                if (isset($opt['page'])) {
                    $this->__page[$name] = $opt['page'];
                    unset($opt['page']);
                    $setRelPaging = true;
                }

                if (isset($opt['pageSize'])) {
                    $this->__pageSize[$name] = $opt['pageSize'];
                    unset($opt['pageSize']);
                }

                if ($setRelPaging) {
                    $criteria = $this->relPagingCriteria($name);
                    $criteria = array_merge($criteria, $opt);
                } else {
                    $criteria = $opt;
                }
            }
            $this->loadRelation($name, @$criteria);
            $this->applyRelChange($name);
            return $this->__relations[$name];
        } else {
            return parent::__call($name, $args);
        }
    }

    private function initRelation() {
        $static = !(isset($this) && get_class($this) == get_called_class());
        if (!$static && !$this->__isRelationLoaded) {
            ## define all relations BUT do not load it on init
            $relMetaData = $this->getMetaData()->relations;
            foreach ($relMetaData as $k => $r) {
                $this->__relations[$k] = [];
            }
            $this->__relations['currentModel'] = [];
            $this->__isRelationLoaded          = true;
        }
    }

    private function relPagingCriteria($name) {
        $page     = @$this->__page[$name] ? $this->__page[$name] : 1;
        $pageSize = $this->{$name . 'PageSize'};
        $start    = ($page - 1) * $pageSize;

        return [
            'limit' => $pageSize,
            'offset' => $start
        ];
    }

    public function loadRelation($name, $criteria = []) {
        if (!isset($this->__relations[$name]))
            return [];

        if (!$criteria) {
            $criteria = [];
        }

        if ($name == 'currentModel' || is_null($name)) {
            $this->__relations['currentModel'] = $this->getRelatedArray($criteria);
        } else {
            $rel   = $this->getMetaData()->relations[$name];
            $class = $rel->className;
            if (!class_exists($class)) {
                return [];
            }

            $relClassType = get_class($rel);
            $tableModel   = $class::model();
            $table        = $tableModel->tableName();
            $tablePKCol   = $tableModel->metadata->tableSchema->primaryKey;

            switch ($relClassType) {
                case 'CHasOneRelation':
                case 'CBelongsToRelation':
                    if (is_string($rel->foreignKey)) {
                        if (($criteria === false || empty($criteria))) {
                            if ($rel->joinType == 'LEFT OUTER JOIN') {
                                $tableModel->tableName();
                                if (!is_null($this[$rel->foreignKey]) && $this[$rel->foreignKey] !== '') {
                                    $this->__relations[$name] = ActiveRecord::queryRow("select * from `{$table}` where `{$tablePKCol}` = {$this[$rel->foreignKey]}");
                                } else {
                                    $this->__relations[$name] = [];
                                }
                            }
                        } else {
                            $this->__relationsObj[$name] = $this->getRelated($name, true, $criteria);
                            if (isset($this->__relationsObj[$name])) {
                                $this->__relations[$name] = $this->__relationsObj[$name]->attributes;

                                foreach ($this->__relations[$name] as $i => $j) {
                                    if (is_array($this->__relations[$name][$i])) {
                                        unset($this->__relations[$name][$i]);
                                    }
                                }
                            }
                        }
                    } else {
                        $this->__relationsObj[$name] = $this->getRelated($name, true, $criteria);
                        if (isset($this->__relationsObj[$name])) {
                            $this->__relations[$name] = $this->__relationsObj[$name]->attributes;

                            foreach ($this->__relations[$name] as $i => $j) {
                                if (is_array($this->__relations[$name][$i])) {
                                    unset($this->__relations[$name][$i]);
                                }
                            }
                        }
                    }
                    break;
                case 'CManyManyRelation':
                case 'CHasManyRelation':
                    //without Criteria
                    $relKey = null;
                    if (is_string($rel->foreignKey) && property_exists($this, $rel->foreignKey)) {
                        $relKey = ($this->{$rel->foreignKey});
                    }

                    if (($criteria === false || empty($criteria)) && is_string($rel->foreignKey) && is_numeric($relKey)) {
                        if ($rel->joinType == 'LEFT OUTER JOIN') {
                            $this->__relations[$name] = ActiveRecord::queryAll("select * from `{$table}` where `{$rel->foreignKey}` = {$this->id} limit 10");
                        }
                    } else {
                        $this->__relationsObj[$name] = $this->getRelated($name, true, $criteria);
                        if (is_array($this->__relationsObj[$name])) {
                            $this->__relations[$name] = [];
                            foreach ($this->__relationsObj[$name] as $i => $j) {
                                $this->__relations[$name][$i] = $j->getAttributes(true, false);
                            }
                        }
                    }
                    break;
                case 'CStatRelation':
                    $this->__relations[$name] = $this->getRelated($name, true, $criteria);
                    break;
            }

            if (isset($criteria['aggregate'])) {
                $criteriaAggregate = $criteria['aggregate'];
                unset($criteria['aggregate']);
                $cdbCriteria = new CDbCriteria($criteria);
                $tableSchema = $tableModel->metadata->tableSchema;
                $this->processAggregate($this->__relations[$name], $criteriaAggregate, $tableSchema, $cdbCriteria);
            }
        }
        return $this->__relations[$name];
    }

    public function getRelatedArray($criteria = [], $rel = null) {

        ## clean criteria array
        if (isset($criteria['page'])) {
            $criteria['offset'] = ($criteria['page'] - 1) * $criteria['pageSize'];
            $criteria['limit']  = $criteria['pageSize'];
            unset($criteria['page'], $criteria['pageSize']);
        }

        if (isset($criteria['nolimit'])) {
            unset($criteria['nolimit']);
        }

        if (isset($criteria['paging']))
            unset($criteria['paging']);

        $tableSchema       = $this->tableSchema;
        $builder           = $this->commandBuilder;
        $criteriaAggregate = null;

        if (isset($criteria['aggregate'])) {
            $criteriaAggregate = $criteria['aggregate'];
            unset($criteria['aggregate']);
        }

        ## find
        $cdbCriteria = new CDbCriteria($criteria);
        if (!@$criteria['distinct'] && !trim(@$criteria['select'])) {
            $cdbCriteria->select = '*';
        }

        ## generate sql;
        $command = $builder->createFindCommand($tableSchema, $cdbCriteria);
        $sql     = $command->text;

        ## execute query
        $rawData = $this->dbConnection->createCommand($sql)->queryAll(true, $cdbCriteria->params);

        ## aggregate
        if (isset($criteriaAggregate)) {
            $this->processAggregate($rawData, $criteriaAggregate, $tableSchema, $cdbCriteria);
        }

        return $rawData;
    }

    private function processAggregate(&$rawData, $criteriaAggregate, $cdbCriteria) {
        $ag = new ArrayGroup($rawData, $criteriaAggregate['groups'], $criteriaAggregate['columns']);
        $ag->group();

        foreach ($criteriaAggregate['groups'] as $lvl => $group) {
            if ($group['mode'] == 'sql' && isset($group['sql']) && trim($group['sql']) != '') {
                $cdbCriteria->limit = -1;
                $cdbCriteria->order = '';
                $builder            = $this->commandBuilder;
                $tableSchema        = $this->tableSchema;
                $command            = $builder->createFindCommand($tableSchema, $cdbCriteria);
                $sql                = $command->text;
                $sql                = str_replace("{sql}", $sql, $group['sql']);
                $sqlGroup           = $this->dbConnection->createCommand($sql)->queryAll(true, $cdbCriteria->params);

                foreach ($sqlGroup as $sg) {
                    $cursor = &$ag->grouped;

                    if ($lvl > 0) {
                        $skip = false;
                        for ($clvl = 1; $clvl <= $lvl; $clvl++) {
                            $sgval  = $sg[$criteriaAggregate['groups'][$clvl]['col']];
                            $cursor = &$cursor['$items'];

                            if (isset($cursor[$sgval])) {
                                $cursor = &$cursor[$sgval];
                            } else {
                                $skip = true;
                                break;
                            }
                        }

                        if ($skip) continue;
                    }

                    $aggregate = &$cursor['$aggregate'];
                    foreach ($criteriaAggregate['columns'] as $c => $aggr) {
                        if (isset($sg[$c])) {
                            $aggregate[$c] = $sg[$c];
                        }
                    }
                }
            }
        }
        $rawData = $ag->flatten();
    }

    public static function queryRow($sql, $params = []) {
        return Yii::app()->db->createCommand($sql)->queryRow(true, $params);
    }

    public static function queryAll($sql, $params = []) {
        return Yii::app()->db->createCommand($sql)->queryAll(true, $params);
    }

    private function applyRelChange($name) {
        $pk = $this->tableSchema->primaryKey;
        $relHash = [];
        foreach ($this->__relations[$name] as $q => $r) {
            if (is_array($r) && isset($r[$pk])) {
                $relHash['_' . $r[$pk]] = ['idx' => $q, 'data' => $r];
            }
        }
        if (count(@$this->__relDelete[$name]) > 0) {
            foreach ($this->__relDelete[$name] as $item) {
                if (is_array($item)) {
                    $rpk = '_' . $item[$pk];
                    if (isset($relHash[$rpk])) {
                        array_splice($this->__relations[$name], $relHash[$rpk]['idx'], 1);
                    }
                } else {
                    if (isset($relHash[$item])) {
                        array_splice($this->__relations[$name], $relHash[$item]['idx'], 1);
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
                $rpk = '_' . $i[$pk];
                if (isset($relHash[$rpk])) {
                    $this->__relations[$name][$relHash[$rpk]['idx']] = $i;
                } else {
                    $this->__relations[$name][] = $i;
                }
            }
        }
    }

    public function isTemp($name) {
        return isset($this->__tempVar);
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
                    } else if ($name == 'currentModel') {
                        $tableSchema  = $this->tableSchema;
                        $builder      = $this->commandBuilder;
                        $countCommand = $builder->createCountCommand($tableSchema, new CDbCriteria);
                        return $countCommand->queryScalar();
                    } else {
                        $c = $this->getRelated($name, true, [
                            'select' => 'count(1) as id',
                        ]);

                        return $c[0]->id;
                    }
                }
                break;
            case Helper::isLastString($name, 'PageSize'):
                $name = substr_replace($name, '', -8);
                if (isset($this->__pageSize[$name])) {
                    return $this->__pageSize[$name];
                } else {
                    return ActiveRecord::DEFAULT_PAGE_SIZE;
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
            case ($name == 'currentModel'):
                return $this->loadRelation($name);
                break;
            case (isset($this->__relations[$name])):
                if (empty($this->__relations[$name])) {
                    $this->loadRelation($name);
                }
                return $this->__relations[$name];
                break;
            case (isset($this->__tempVar[$name])):
                return $this->__tempVar;
                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    public function __set($name, $value) {
        $this->initRelation();
        switch (true) {
            case Helper::isLastString($name, 'PageSize'):
                $name                    = substr_replace($name, '', -8);
                $this->__pageSize[$name] = $value;
                break;
            case Helper::isLastString($name, 'Insert'):
                $name = substr_replace($name, '', -6);
                if (isset($this->__relations[$name])) {
                    $this->__relInsert[$name] = $value;
                }
                break;
            case Helper::isLastString($name, 'Update'):
                $name = substr_replace($name, '', -6);

                if (isset($this->__relations[$name])) {
                    $this->__relUpdate[$name] = $value;
                }
                break;
            case Helper::isLastString($name, 'Delete'):
                $name = substr_replace($name, '', -6);
                if (isset($this->__relations[$name])) {
                    $this->__relDelete[$name] = $value;
                }
                break;
            case isset($this->__relations[$name]):
                $this->__relations[$name] = $value;
                break;
            default:
                try {
                    parent::__set($name, $value);
                } catch (Exception $e) {
                    $this->__tempVar[$name] = $value;
                }
                break;
        }
    }

    public function loadAllRelations() {
        $relMetaData = $this->getMetaData()->relations;
        foreach ($relMetaData as $k => $r) {
            $this->__relations[$k] = [];
            $this->loadRelation($k, false);
        }
    }

    /**
     * Creates an active record with the given attributes.
     * This method is internally used by the find methods.
     * @param array $attributes attribute values (column name=>column value)
     * @param boolean $callAfterFind whether to call {@link afterFind} after the record is populated.
     * @return CActiveRecord the newly created active record. The class of the object is the same as the model class.
     * Null is returned if the input data is false.
     */
    public function populateRecord($attributes, $callAfterFind = true) {
        $record = parent::populateRecord($attributes, $callAfterFind);

        if (is_subclass_of($record, 'ActiveRecord')) {
            foreach ($attributes as $k => $a) {
                if (!isset($record->{$k})) {
                    $record->{$k} = $a;
                }
            }
        }

        return $record;
    }

    public function getRelChanges($name) {
        return [
            'insert' => count(@$this->__relInsert[$name]) > 0 ? @$this->__relInsert[$name] : [],
            'update' => count(@$this->__relUpdate[$name]) > 0 ? @$this->__relUpdate[$name] : [],
            'delete' => count(@$this->__relDelete[$name]) > 0 ? @$this->__relDelete[$name] : [],
        ];
    }
        
    public function resetRelChanges($relation) {
        $this->__relInsert[$relation] = [];
        $this->__relUpdate[$relation] = [];
        $this->__relDelete[$relation] = [];
    }

    public function resetRel($relation, $data = null) {
        $this->__relInsert[$relation] = [];
        $this->__relUpdate[$relation] = [];
        $this->__relReset[]           = $relation;

        if (is_null($data)) {
            if (isset($this->__relations[$relation])) {
                $data = $this->__relations[$relation];
            } else {
                $data = [];
            }
        }

        foreach ($data as $d) {
            if (isset($d['id']) && is_numeric($d['id'])) {
                $this->__relUpdate[$relation][] = $d;
            } else {
                $this->__relInsert[$relation][] = $d;
            }
        }
    }

    public function setAttributes($values, $safeOnly = false, $withRelation = true) {
        parent::setAttributes($values, $safeOnly);
        $this->initRelation();
        foreach ($this->__relations as $k => $r) { 
            if ($k != 'currentModel' && isset($values[$k])) {
                $rel                   = $this->getMetaData()->relations[$k];
                $this->__relations[$k] = $values[$k];

                if (is_string($values[$k]) || (is_array($values[$k]))) {
                    if (is_string($values[$k])) {
                        $attr = json_decode($values[$k], true);
                        if (!is_array($attr)) {
                            $attr = [];
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
                $value                 = $values[$k . 'Insert'];
                $value                 = is_string($value) ? json_decode($value, true) : $value;
                $this->__relInsert[$k] = $value;
            }

            if (isset($values[$k . 'Update'])) {
                $value                 = $values[$k . 'Update'];
                $value                 = is_string($value) ? json_decode($value, true) : $value;
                $this->__relUpdate[$k] = $value;
            }

            if (isset($values[$k . 'Delete'])) {
                $value                 = $values[$k . 'Delete'];
                $value                 = is_string($value) ? json_decode($value, true) : $value;
                $this->__relDelete[$k] = $value;
            }

            $this->applyRelChange($k);
            
        }
        
        $ap = $this->getAttributeProperties();
        foreach ($ap as $k => $r) {
            if (isset($values[$k])) {
                $this->$k = $values[$k];
            }
        }
    }

    public function getAttributeProperties() {
        $props      = [];
        $class      = new ReflectionClass($this);
        $properties = Helper::getClassProperties($this);

        foreach ($this->__tempVar as $k => $p) {
            $props[$k] = $p;
        }
        foreach ($properties as $p) {
            $props[$p->name] = $this->{$p->name};
        }
        return $props;
    }

    public function getAttributesRelated($names = true) {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($attributes, $this->__relations);
        $attributes = array_merge($this->attributeProperties, $attributes);

        return $attributes;
    }

    public function getAttributesList($names = true) {
        $fields    = [];
        $props     = [];
        $relations = [];
        $attrs     = parent::getAttributes($names);
        foreach ($attrs as $k => $i) {
            $fields[$k] = $k;
        }

        foreach ($this->getMetaData()->relations as $k => $r) {
            if (!isset($fields[$k])) {
                if (@class_exists($r->className)) {
                    $relations[$k] = $k;
                } else {
                    continue;
                    throw new CException('Failed to load relation "' . $k . '" in ' . get_class($this));
                }
            }
        }

        foreach ($this->attributeProperties as $k => $r) {
            $props[$k] = $k;
        }

        $attributes = ['DB Fields' => $fields];

        if (count($props) > 0) {
            $attributes = $attributes + ['Properties' => $props];
        }

        if (count($relations) > 0) {
            $attributes = $attributes + ['Relations' => $relations];
        }

        return $attributes;
    }

    public function beforeValidate() {
        $validator = new CInlineValidator;
        $validator->method = 'relationValidator';
        
        foreach ($this->__relations as $k => $new) { 
            if ($k == 'currentModel' && count($this->__relations[$k]) > 0) {
                $validator->attributes[] = $k;
            } else {
                $rel = @$this->metaData->relations[$k];
                if (!!$rel) {
                    $modelClass = $rel->className;
                    if (class_exists($modelClass)) {
                        if (count($this->__relations[$k]) > 0) {
                            $validator->attributes[] = $k;
                        }
                    }
                }
            }
        }
        
        if (!empty($validator->attributes)) {
            $this->validatorList->add($validator);
        }
        
        return parent::beforeValidate();
    }
    
    public function relationValidator($relName) {
        $pk = $this->tableSchema->primaryKey;
        if ($relName == 'currentModel')  {
            $rel = new CHasManyRelation('currentModel', get_class($this), $pk);
        } else {
            $rel = @$this->metaData->relations[$relName];
        }
        
        if (!!$rel) {
            $modelClass = $rel->className;
            $model = new $modelClass;
            $relPK = $model->tableSchema->primaryKey;
            $errors = [
                'type' => get_class($rel),
                'list' => []
            ];
            
            $idx = ['insert'=>0,'edit'=>0];
            switch (get_class($rel)) {
                case 'CHasManyRelation':
                case 'CManyManyRelation': 
                    foreach ($this->__relations[$relName] as $k=>$r) {
                        $model = new $modelClass;
                        $model->attributes = $r;
                        $model->{$rel->foreignKey} = $this->isNewRecord ? '0' : $this->{$pk};
                        if (!$model->validate()) {
                            if (isset($r['$rowState'])) {
                                $errors['list'][] = [
                                    'index' => $idx[$r['$rowState']] ,
                                    'mode' => $r['$rowState'],
                                    'errors' => $model->errors
                                ];
                                $idx[$r['$rowState']]++;
                                
                                if ($r['$rowState'] == 'edit') {
                                    $relHash['_' . $r[$relPK]] = $r;
                                }
                            }
                        }
                    }
                break;
            }
            
            if (!empty($errors['list'])) {
                $this->addError($relName, $errors);
            }
        }
    }

    public function save($runValidation = true, $attributes = null) {
        if (!$runValidation || $this->validate($attributes)) {
            try {
                return $this->getIsNewRecord() ? $this->insert($attributes) : $this->update($attributes);
            } catch (CDbException $e) {
                if (@$e->errorInfo[1] == 1452) {
                    preg_match("/FOREIGN\sKEY\s\(\`(.*)\`\)\sREFERENCES/", $e->errorInfo[2], $match);
                    $attribute = explode("`", $match[1]);
                    $attribute = @$attribute[0];

                    if ($this->hasAttribute($attribute)) {
                        $message = Yii::t('yii', '{attribute} cannot be blank.');
                        $message = strtr($message, array(
                            '{attribute}' => $this->getAttributeLabel($attribute),
                        ));
                        $this->addError($attribute, $message);
                    }
                } else {
                    throw $e;
                }
            }
        } else {
            return false;
        }
    }

    public function saveModelArray() {
        $this->afterSave();
    }

    public function afterSave() {
        return $this->doAfterSave();
    }

    public function saveRelation() {
        $pk = $this->tableSchema->primaryKey;
        foreach ($this->__relations as $k => $new) {
            if ($k == 'currentModel') {
                $rel = new CHasManyRelation('currentModel', get_class($this), $pk);
            } else {
                $rel = $this->getMetaData()->relations[$k];
            }

            $relClass = $rel->className;
            if (!class_exists($relClass))
                continue;

            $relType       = get_class($rel);
            $relForeignKey = $rel->foreignKey;
            $relTableModel = $relClass::model();
            $relTable      = $relTableModel->tableName();
            $relPK         = $relTableModel->metadata->tableSchema->primaryKey;

            switch ($relType) {
                case 'CHasOneRelation':
                case 'CBelongsToRelation':
                    if (!empty($new)) {
                        $relForeignKey = $rel->foreignKey;
                        if ($this->{$relForeignKey} == $new[$relPK]) {
                            $model = $relClass::model()->findByPk($this->{$relForeignKey});
                            if (is_null($model)) {
                                $model = new $relClass;
                            }
                            
                            if (array_diff($model->getAttributesWithoutRelation(), $new)) {
                                $model->attributes = $new;
                                if ($relType == 'CHasOneRelation') {
                                    $model->{$relForeignKey} = $this->{$pk};
                                }
                                $model->save();
                            }
                        } else {
                            $this->loadRelation($rel->name);
                        }
                    }
                    break;
                case 'CManyManyRelation':
                case 'CHasManyRelation':
                    ## if relation type is Many to Many, prepare required variable
                    $relMM = [];
                    if ($relType == 'CManyManyRelation') {
                        $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);
                        $stmts  = $parser->parse('<?php ' . $relForeignKey . ';');
                        if (count($stmts) > 0) {
                            $relMM = [
                                'tableName' => $stmts[0]->name->parts[0],
                                'from' => $stmts[0]->args[0]->value->name->parts[0],
                                'to' => $stmts[0]->args[1]->value->name->parts[0]
                            ];
                        }
                    }

                    ## Handle Insert
                    if (isset($this->__relInsert[$k])) {
                        if ($k != 'currentModel') {
                            if (is_string($relForeignKey)) { ## without through
                                if ($relType != 'CManyManyRelation') {
                                    foreach ($this->__relInsert[$k] as $n => $m) {
                                        $this->__relInsert[$k][$n][$relForeignKey] = $this->{$pk};
                                    }
                                }
                            } else if (is_array($relForeignKey)) { ## with through
                                foreach ($this->__relInsert[$k] as $n => $m) {
                                    foreach ($relForeignKey as $rk => $fk) {
                                        $this->__relInsert[$k][$n][$fk] = $this->__relations[$rel->through][$rk];
                                    }
                                }
                            }
                        }

                        if (count($this->__relInsert[$k]) > 0) {
                            if ($relType == "CHasManyRelation") {
                                ActiveRecord::batchInsert($relClass, $this->__relInsert[$k]);
                            }

                            ## if current relation is many to many
                            if ($relType == 'CManyManyRelation' && !empty($relMM)) {
                                $manyRel = [];
                                foreach ($this->__relInsert[$k] as $item) {
                                    $manyRel[] = [
                                        $relMM['from'] => $this->{$pk},
                                        $relMM['to'] => $item[$relPK]
                                    ];
                                }

                                ## if relinsert is already exist, then do not insert it again
                                foreach ($this->__relInsert[$k] as $insIdx => &$ins) {
                                    if (!!@$ins[$relPK]) {
                                        unset($this->__relInsert[$k]);
                                    }
                                }
                                ActiveRecord::batchInsert($relClass, $this->__relInsert[$k]);

                                ## create transaction entry to link between
                                ## related model and current model
                                ActiveRecord::batchInsert($relMM['tableName'], $manyRel, false);

                            }
                        }
                        $this->__relInsert[$k] = [];
                    }

                    ## Handle Update
                    if (isset($this->__relUpdate[$k])) {
                        if ($k != 'currentModel') {
                            if (is_string($relForeignKey)) { ## without through
                                if ($relType == 'CManyManyRelation') {

                                } else {
                                    foreach ($this->__relUpdate[$k] as $n => $m) {
                                        $this->__relUpdate[$k][$n][$relForeignKey] = $this->{$pk};
                                    }
                                }
                            } else if (is_array($relForeignKey)) { ## with through
                                foreach ($this->__relUpdate[$k] as $n => $m) {
                                    foreach ($relForeignKey as $rk => $fk) {
                                        $this->__relUpdate[$k][$n][$fk] = $this->__relations[$rel->through][$rk];
                                    }
                                }
                            }
                        }

                        if (count($this->__relUpdate[$k]) > 0) {
                            ActiveRecord::batchUpdate($relClass, $this->__relUpdate[$k]);
                        }
                        $this->__relUpdate[$k] = [];
                    }

                    ## Handle Delete
                    if (isset($this->__relDelete[$k])) {
                        if (count($this->__relDelete[$k]) > 0) {
                            if ($relType == 'CManyManyRelation') {
                                if (!empty($relMM)) {
                                    //first remove entry in transaction table first
                                    ActiveRecord::batchDelete($relMM['tableName'], $this->__relDelete[$k], [
                                        'table' => $relMM['tableName'],
                                        'pk' => $relPK,
                                        'condition' => "{$relMM['from']} = {$this->{$pk}} AND {$relMM['to']} IN (:ids)",
                                        'integrityError' => false
                                    ]);

                                    //and then remove entry in actual table
                                    //ActiveRecord::batchDelete($relClass, $this->__relDelete[$k]);
                                }
                            } else {
                                ActiveRecord::batchDelete($relClass, $this->__relDelete[$k]);
                            }
                        }
                        $this->__relDelete[$k] = [];
                    }
                    break;
            }
        }
    }

    public function doAfterSave($withRelation = true) {
        $pk = $this->tableSchema->primaryKey;

        if ($this->isNewRecord) {
            $this->{$pk} = $this->dbConnection->getLastInsertID(); ## this is hack
            ## UPDATE AUDIT TRAIL 'CREATE' ID
            if (!!Yii::app()->user && !Yii::app()->user->isGuest) {
                $a = $this->dbConnection->createCommand("
                update p_audit_trail set model_id = :model_id
                WHERE user_id = :user_id and
                model_class = :model_class and
                type = 'create' and
                model_id is null")->execute([
                    'model_class' => ActiveRecord::baseClass($this),
                    'model_id' => $this->{$pk},
                    'user_id' => Yii::app()->user->id
                ]);
            }
        } else {
            $this->deleteResetedRelations();
        }

        if ($withRelation) {
            $this->saveRelation();
        }

        ## handling untuk file upload
        if (method_exists($this, 'getFields')) {
            $fb           = FormBuilder::load(get_class($this));
            $uploadFields = $fb->findAllField(['type' => 'UploadFile']);
            $attrs        = [];
            $model        = $this;

            foreach ($uploadFields as $k => $f) {
                if (@$f['name'] == '' || @$f['uploadPath'] == '') {
                    continue;
                }

                ## create directory
                ## Jika disini gagal, berarti ada yang salah dengan format uploadPath di FormBuilder-nya
                $evalDir = '';
                eval('$evalDir = "' . $f['uploadPath'] . '";');
                $evalDir    = str_replace(["\n", "\r"], "", $evalDir);
                $repopath   = realpath(Yii::getPathOfAlias("repo"));
                $evalDirArr = explode("/", $evalDir);
                foreach ($evalDirArr as $i => $j) {
                    $evalDirArr[$i] = preg_replace('/[\/\?\:\*\"\<\>\|\\\]*/', "", $j);
                }
                $evalDir = implode("/", $evalDirArr);
                $dir     = $repopath . "/" . $evalDir . "/";
                $dir     = str_replace(["\n", "\r"], "", $dir);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                ## get oldname
                $old      = $this->{$f['name']};
                $ext      = pathinfo($old, PATHINFO_EXTENSION);
                $filename = pathinfo($old, PATHINFO_FILENAME);

                if (@$f['filePattern']) {
                    ## get newname
                    ## Jika disini gagal, berarti ada yang salah dengan format filePattern di FormBuilder-nya
                    eval('$newname = "' . $f['filePattern'] . '";');
                } else {
                    $newname = $filename . "." . $ext;
                }

                $new = $dir . preg_replace('/[\/\?\:\*\"\<\>\|\\\]*/', "", $newname);
                $new = str_replace(["\n", "\r"], "", $new);

                ## delete file if already exist and allowed to overwrite
                if (is_file($new) && $f['allowOverwrite'] == 'Yes' && is_file($old)) {
                    unlink($new);
                }

                if (!is_file($new) && is_file($old)) {
                    rename($old, $new);
                    $this->{$f['name']} = trim($evalDir, "/") . "/" . $newname;
                    $attrs[]            = $f['name'];
                }
            }


            if (count($attrs) > 0) {
                if ($this->isNewRecord) {
                    $this->isNewRecord = false;
                    $this->updateByPk($this->id, $this->getAttributes($attrs));
                    $this->isNewRecord = true;
                } else {
                    $this->update($attrs);
                }
            }
        }

        return true;
    }

    public static function baseClass($object) {
        $class   = new ReflectionClass($object);
        $lineage = array();
        $prev    = "";
        $c       = $class->getParentClass()->name;

        if ($c == "ActiveRecord" || $c == "CActiveRecord")
            return get_class($object);

        while ($class = $class->getParentClass()) {
            $c = $class->getName();

            if ($c == "ActiveRecord" || $c == "CActiveRecord")
                return $prev;

            $prev = $c;
        }

        return false;
    }

    public function deleteResetedRelations() {
        ## delete all relation data that not included in relUpdate..
        $rels = $this->getMetaData()->relations;
        $pk = $this->tableSchema->primaryKey;
        foreach ($this->__relReset as $r) {
            if (!isset($rels[$r])) {
                continue;
            }
            $rel = $rels[$r];
            switch (get_class($rel)) {
                case 'CManyManyRelation':
                case 'CHasManyRelation':
                    ## without through
                    if (is_string($rel->foreignKey)) {
                        $class     = $rel->className;
                        $tableName = $class::model()->tableName();

                        $ids = [];
                        foreach ($this->__relUpdate[$r] as $u) {
                            $ids[] = $u[$pk];
                        }
                        if (!empty($ids)) {
                            $ids   = implode(",", $ids);
                            $where = "where {$pk} not in ($ids) AND {$rel->foreignKey} = " . $this->{$pk};
                        }
                    } ## todo: with through
                    else {

                    }
                    break;
            }
        }
    }

    public function getAttributesWithoutRelation($names = true) {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($this->attributeProperties, $attributes);

        return $attributes;
    }
    
    public function getAttributes($names = true) {
        $attributes = parent::getAttributes($names);
        $attributes = array_merge($this->attributeProperties, $attributes);

        foreach ($this->__relations as $k => $r) {
            $attributes[$k] = $this->__relations[$k];
        }

        return $attributes;
    }

    public function getModelFieldList() {
        $fields = array_keys(parent::getAttributes());

        foreach ($fields as $k => $f) {
            if ($this->tableSchema->primaryKey == $f) {
                $type = "HiddenField";
            } else {
                $type = "TextField";
            }

            $array[] = [
                'name' => $f,
                'type' => $type,
                'label' => $this->getAttributeLabel($f)
            ];
        }
        return $array;
    }

    public function delete() {
        try {
            if (!!$this->_softDelete) {
                $this->{$this->_softDelete['column']} = $this->_softDelete['value'];
                $this->update([$this->_softDelete['column']]);
            } else {
                parent::delete();
            }
        } catch (CDbException $e) {
            if ($e->errorInfo[0] == "23000") {
                Yii::app()->controller->redirect(["/site/error&id=integrity&msg=" . $e->errorInfo[2]]);
            }
        }
    }

    public function getDefaultFields($options = []) {
        Yii::import("application.components.codegen.templates.ActiveRecordTemplate");
        return ActiveRecordTemplate::generateFields($this, $options);
    }


}
