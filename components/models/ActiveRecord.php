<?php

class ActiveRecord extends CActiveRecord {

    private $__relations = [];
    private $__relationsObj = [];
    private $__oldRelations = [];
    private $__isRelationLoaded = false;
    private $__defaultPageSize = 25;
    private $__pageSize = [];
    private $__page = [];
    private $__relInsert = [];
    private $__relUpdate = [];
    private $__relDelete = [];
    private $__tempVar = [];

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

        return [
            'limit' => $pageSize,
            'offset' => $start
        ];
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

            $this->loadRelations($name, @$criteria);
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
                try {
                    parent::__set($name, $value);
                } catch (Exception $e) {
                    $this->__tempVar[$name] = $value;
                }
                break;
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
                        $tableSchema = $this->tableSchema;
                        $builder = $this->commandBuilder;
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
            case ($name == 'currentModel' || isset($this->getMetaData()->relations[$name])):
                $this->initRelation();
                return @$this->__relations[$name];
                break;
            case (isset($this->__tempVar[$name])):
                return $this->__tempVar;
                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    public static function jsonToArray(&$post, $data) {
        if (isset($post[$data . 'Insert']) && is_string($post[$data . 'Insert']))
            $post[$data . 'Insert'] = json_decode($post[$data . 'Insert'], true);

        if (isset($post[$data . 'Update']) && is_string($post[$data . 'Update']))
            $post[$data . 'Update'] = json_decode($post[$data . 'Update'], true);

        if (isset($post[$data . 'Delete']) && is_string($post[$data . 'Delete']))
            $post[$data . 'Delete'] = json_decode($post[$data . 'Delete'], true);
    }

    public static function toArray($models = []) {
        $result = [];
        foreach ($models as $k => $m) {
            $result[$k] = $m->attributes;
        }
        return $result;
    }

    public function getModelArray($criteria = []) {

        if (isset($criteria['page'])) {
            $criteria['offset'] = ($criteria['page'] - 1) * $criteria['pageSize'];
            $criteria['limit'] = $criteria['pageSize'];
            unset($criteria['page'], $criteria['pageSize']);
        } else if (!isset($criteria['limit'])) {
            $criteria['limit'] = 25;
        }

        if (isset($criteria['paging']))
            unset($criteria['paging']);

        $tableSchema = $this->tableSchema;
        $builder = $this->commandBuilder;

        ## find
        $cdbCriteria = new CDbCriteria($criteria);
        $command = $builder->createFindCommand($tableSchema, $cdbCriteria);

        if (!@$criteria['distinct']) {
            $rawData = $command->select('*')->queryAll();
        } else {
            $rawData = $command->queryAll();
        }

        return $rawData;
    }

    public function loadRelations($name = null, $criteria = []) {
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
                                $table = $class::model()->tableName();
                                $this->__relationsObj[$k] = $this->getRelated($k, true, $criteria);
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
                            $this->__relationsObj[$k] = $this->getRelated($k, true, $criteria);

                            if (is_array($this->__relationsObj[$k])) {
                                $this->__relations[$k] = [];

                                foreach ($this->__relationsObj[$k] as $i => $j) {
                                    $this->__relations[$k][$i] = $j->getAttributes(true, false);
                                }
                            }
                            //with through
                            //todo..
                            break;
                    }
                }
            }
        }
        if ($name == 'currentModel' || is_null($name)) {


            $this->__relations['currentModel'] = $this->getModelArray($criteria);
        }

        $this->__isRelationLoaded = true;
        $this->__oldRelations = $this->__relations;
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
            if ($k != 'currentModel' && isset($values[$k])) {
                $rel = $this->getMetaData()->relations[$k];
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

    public function getAttributes($names = true, $withRelation = false) {
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
        $props = [];
        $class = new ReflectionClass($this);
        $properties = Helper::getClassProperties($this);

        foreach ($this->__tempVar as $k => $p) {
            $props[$k] = $p;
        }
        foreach ($properties as $p) {
            $props[$p->name] = $this->{$p->name};
        }
        return $props;
    }

    public function getAttributesList($names = true) {
        $fields = [];
        $props = [];
        $relations = [];
        $attrs = parent::getAttributes($names);
        foreach ($attrs as $k => $i) {
            $fields[$k] = $k;
        }

        foreach ($this->getMetaData()->relations as $k => $r) {
            if (!isset($fields[$k])) {
                if (@class_exists($r->className)) {
                    $relations[$k] = $k;
                } else {
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

    public function beforeSave() {
        ## clean primary keys
        if ($this->primaryKey == '') {
            $table = $this->getMetaData()->tableSchema;
            $primaryKey = $table->primaryKey;
            $this->$primaryKey = null;
        }

        ## clean relation columns
        foreach ($this->getMetaData()->relations as $k => $rel) {
            $single = get_class($rel) == 'CHasOneRelation' || get_class($rel) == 'CBelongsToRelation';
            if ($single && is_string($rel->foreignKey)) {
                if (!is_numeric($this[$rel->foreignKey]) && !is_null($this[$rel->foreignKey])) {
                    $this[$rel->foreignKey] = null;
                }
            }
        }


        return true;
    }

    public function saveModelArray() {
        $this->afterSave();
    }

    public function afterSave() {
        if ($this->isNewRecord) {
            $this->id = Yii::app()->db->getLastInsertID(); // this is hack
        }

        foreach ($this->__relations as $k => $new) {
            $new = $new == '' ? [] : $new;
            $old = @$this->__oldRelations[$k];
            if ((is_array($new) && is_array($old))) {

                if ($k == 'currentModel') {
                    $rel = new CHasManyRelation('currentModel', get_class($this), 'id');
                } else {
                    $rel = $this->getMetaData()->relations[$k];
                }
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
                                if ($k != 'currentModel') {
                                    foreach ($this->__relInsert[$k] as $n => $m) {
                                        $this->__relInsert[$k][$n][$rel->foreignKey] = $this->id;
                                    }
                                }
                                if (count($this->__relInsert[$k]) > 0) {
                                    ActiveRecord::batchInsert($class, $this->__relInsert[$k]);
                                }
                                $this->__relInsert[$k] = [];
                            }

                            if (isset($this->__relUpdate[$k])) {
                                if ($k != 'currentModel') {
                                    foreach ($this->__relUpdate[$k] as $n => $m) {
                                        $this->__relUpdate[$k][$n][$rel->foreignKey] = $this->id;
                                    }
                                }

                                if (count($this->__relUpdate[$k]) > 0) {
                                    ActiveRecord::batchUpdate($class, $this->__relUpdate[$k]);
                                }
                                $this->__relUpdate[$k] = [];
                            }

                            if (isset($this->__relDelete[$k])) {
                                if (count($this->__relDelete[$k]) > 0) {
                                    ActiveRecord::batchDelete($class, $this->__relDelete[$k]);
                                }
                                $this->__relDelete[$k] = [];
                            }
                        } elseif (is_array($rel->foreignKey)) {
                            $class = $rel->className;

                            if (isset($this->__relInsert[$k])) {
                                if ($k != 'currentModel') {
                                    foreach ($this->__relInsert[$k] as $n => $m) {
                                        foreach ($rel->foreignKey as $rk => $fk) {
                                            $this->__relInsert[$k][$n][$fk] = $this->__relations[$rel->through][$rk];
                                        }
                                    }
                                }
                                if (count($this->__relInsert[$k]) > 0) {
                                    ActiveRecord::batchInsert($class, $this->__relInsert[$k]);
                                }
                                $this->__relInsert[$k] = [];
                            }

                            if (isset($this->__relUpdate[$k])) {
                                if ($k != 'currentModel') {
                                    foreach ($this->__relUpdate[$k] as $n => $m) {
                                        foreach ($rel->foreignKey as $rk => $fk) {
                                            $this->__relUpdate[$k][$n][$fk] = $this->__relations[$rel->through][$rk];
                                        }
                                    }
                                }

                                if (count($this->__relUpdate[$k]) > 0) {
                                    ActiveRecord::batchUpdate($class, $this->__relUpdate[$k]);
                                }
                                $this->__relUpdate[$k] = [];
                            }

                            if (isset($this->__relDelete[$k])) {
                                if (count($this->__relDelete[$k]) > 0) {
                                    ActiveRecord::batchDelete($class, $this->__relDelete[$k]);
                                }
                                $this->__relDelete[$k] = [];
                            }
                        }
                        //with through
                        //todo..
                        break;
                }
            }
            $this->__relations[$k] = $new;
        }


        ## handling untuk file upload
        if (method_exists($this, 'getFields')) {
            $fb = FormBuilder::load(get_class($this));
            $uploadFields = $fb->findAllField(['type' => 'UploadFile']);
            $attrs = [];
            $model = $this;

            foreach ($uploadFields as $k => $f) {
                if (@$f['name'] == '' || @$f['uploadPath'] == '') {
                    continue;
                }
                ## create directory
                ## Jika disini gagal, berarti ada yang salah dengan format uploadPath di FormBuilder-nya
                eval('$evalDir = "' . $f['uploadPath'] . '";');
                $evalDir = str_replace(["\n", "\r"], "", $evalDir);
                $repopath = realpath(Yii::getPathOfAlias("repo"));
                $evalDirArr = explode("/", $evalDir);
                foreach ($evalDirArr as $i => $j) {
                    $evalDirArr[$i] = preg_replace('/[\/\?\:\*\"\<\>\|\\\]*/', "", $j);
                }
                $evalDir = implode("/", $evalDirArr);
                $dir = $repopath . "/" . $evalDir . "/";
                $dir = str_replace(["\n", "\r"], "", $dir);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                ## get oldname
                $old = $this->{$f['name']};
                $ext = pathinfo($old, PATHINFO_EXTENSION);
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
                    $attrs[] = $f['name'];
                }
            }

            if (count($attrs) > 0) {
                if ($this->isNewRecord) {
                    $this->isNewRecord = false;
                    $this->update($attrs);
                    $this->isNewRecord = true;
                } else {
                    $this->update($attrs);
                }
            }
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

            $array[] = [
                'name' => $f,
                'type' => $type,
                'label' => $this->getAttributeLabel($f)
            ];
        }
        return $array;
    }

    public static function batchPost($model, $post, $name, $attr = []) {
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

    public static function baseClass($object) {
        $class = new ReflectionClass($object);
        $lineage = array();
        $prev = "";
        while ($class = $class->getParentClass()) {
            $c = $class->getName();

            if ($c == "ActiveRecord" || $c == "CActiveRecord")
                return $prev;

            $prev = $c;
        }

        return false;
    }

    public static function batch($model, $new, $old = [], $delete = true) {
        $deleteArr = [];
        $updateArr = [];

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

        $insertArr = [];
        $insertIds = [];
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
            $ids = [];
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

        $rels = $model::model()->relations();
        $foreignKeys = [];
        foreach ($rels as $r) {
            if ($r[0] == 'CBelongsToRelation' && is_string($r[2])) {
                $foreignKeys[] = $r[2];
            }
        }

        foreach ($data as $d) {
            $cond = $d['id'];
            unset($d['id']);
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
                $update .= "UPDATE {$table} SET {$updatesql} WHERE id='{$cond}';";
            }
        }
        if ($update != '') {
            $command = Yii::app()->db->createCommand($update);
            try {
                $command->execute();
            } catch (Exception $e) {
                echo $update;
                var_dump($data);
                die();
            }
        }
    }


    public static function listTables() {
        $connection = Yii::app()->db;
        $dbSchema = $connection->schema;
        $tables = $dbSchema->getTables();
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

    public function delete() {
        try {
            parent::delete();
        } catch (CDbException $e) {
            if ($e->errorInfo[0] == "23000") {
                Yii::app()->controller->redirect(["/site/error&id=integrity"]);
            }
        }
    }

    public function getDefaultFields() {
        return ActiveRecordForm::generateFields($this);
    }

}
