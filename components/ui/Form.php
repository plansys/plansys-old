<?php

/**
 * Class Form
 * @author rizky
 */
class Form extends CFormModel {

    private $parent; ## used by listview to store parent model
    private $__tempVar = [];

    public function __construct($modelParent = null) {
        parent::__construct('');
        $this->parent = $modelParent;
    }

    public function beforeValidate() {
        $this->validateSubForm();
        return parent::beforeValidate();
    }
    
    public function validateSubForm() {
        $class = get_class($this);
        $fb = FormRenderer::load($class);
        $listView = $fb->findAllField(['type' => 'ListView']);
        foreach($listView as $k => $lv) {
            ## if listview is valid
            if ((@$lv['fieldTemplate'] == "datasource" || @$lv["fieldTemplate"] == "form")  
                && @$lv['templateForm'] != '') {
                
                if (isset($this->attributes[$lv['name']])) {
                    $items = $this->attributes[$lv['name']];
                    if (is_array($items)) {
                        foreach ($items as $k=>$item) {
                            Yii::import($lv['templateForm']);
                            $newClass = Helper::explodeLast(".", $lv['templateForm']);
                            $new = new $newClass;
                            $new->attributes = $item;
                            $new->validate();
                            if ($new->hasErrors()) {
                                foreach ($new->errors as $name=>$errors) {
                                    $this->addError($name, implode("<br> &bull; ", $errors));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array me-return attributes dari form tersebut.
     */
    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

    public function __get($name) {
        switch (true) {
            case (isset($this->__tempVar[$name])):
                return $this->__tempVar;
                break;
            default:
                return parent::__get($name);
                break;
        }
    }

    public function __set($name, $value) {
        try {
            parent::__set($name, $value);
        } catch (Exception $e) {
            $this->__tempVar[$name] = $value;
        }
    }

    /**
     * getAttributes
     * Fungsi ini digunakan untuk mendapatkan attributes field dan me-returnnya
     * @return array me-return array atribut field
     */
    public function getAttributes($names = NULL) {
        $reflect = new ReflectionClass($this);
        $props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $result  = [];
        foreach ($this->__tempVar as $k => $p) {
            $result[$k] = $p;
        }
        foreach ($props as $k => $p) {
            if (!$p->isStatic()) {
                $name          = $p->getName();
                $result[$name] = $this->$name;
            }
        }
        return $result;
    }

    /**
     * @param array $values parameter sebuah array atribut field
     */
    public function setAttributes($values, $safeOnly = true) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    /**
     * @return array me-return array property DefaultFields.
     */
    public function getDefaultFields() {
        $fields  = $this->attributes;
        $exclude = [];
        $array   = [];
        foreach ($fields as $k => $f) {
            $array[] = [
                'name' => $k,
                'type' => 'TextField',
                'label' => ucfirst($k)
            ];
        }
        return $array;
    }

}
