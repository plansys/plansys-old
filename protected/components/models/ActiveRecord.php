<?php

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

abstract class ActiveRecord extends CActiveRecord {

    protected $id; // needed by Doctrine
    private static $_models = array();   // class name => model

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

    /**
     * Returns the static model of the specified AR class.
     * @return the static model class
     */
    public static function model($className = null) {
        if (is_null($className)) {
            $className = get_called_class();
        }
        
        if (isset(self::$_models[$className]))
            return self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
            $model->attachBehaviors($model->behaviors());
            return $model;
        }
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @param mixed $value property value
     */
    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            try {
                parent::__set($name, $value);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * PHP getter magic method.
     * This method is overridden so that AR attributes can be accessed like properties.
     * @param string $name property name
     * @return mixed property value
     * @see getAttribute
     */
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            try {
                $return = parent::__get($name);
            } catch (Exception $e) {
                throw $e;
                $return = null;
            }
            return $return;
        }
    }

    public function getDefaultFields() {
        $fields = array_keys($this->attributes);
        $return = array();
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

        array_unshift($array, array(
            'type' => 'Text',
            'value' => '<h2><center>{{ form.formTitle }}</center></h2><hr/>'
        ));
        array_push($array, array(
            'label' => 'Submit',
            'type' => 'SubmitButton',
        ));
        return $array;
    }

    public static function loadMetadata(ClassMetadata $metadata) {
        $builder = new ClassMetadataBuilder($metadata);
        if (!isset($metadata->fieldMappings['id'])) {
            $metadata->mapField(array(
                'fieldName' => 'id',
                'type' => 'integer',
                'id' => true,
                'columnName' => 'id',
            ));
        }

        if ($metadata->name != "ActiveRecord") {
            $model = static::model();

            unset($metadata->fieldMappings['id']['inherited']);
            unset($metadata->fieldMappings['id']['declared']);

            $tableName = $model->tableName();
            if ($tableName != "") {
                $metadata->setTableName($tableName);
            }

            foreach ($model->attributes as $key => $val) {
                if ($key == 'id')
                    continue;

                $builder->addField($key, 'string');
            }

            foreach ($model->relations() as $field => $relation) {
                switch ($relation[0]) {
                    case self::BELONGS_TO:
                        $builder->addManyToOne($field, $relation[1]);
                        break;
                }
            }
        }
    }

}
