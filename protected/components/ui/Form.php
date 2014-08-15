<?php

class Form extends CComponent {

    private $_errors = array();
    public function getErrors() {
        return $this->_errors;
    }
    
    public function setErrors($value) {
        $this->_errors = $value;
    }
    
    public function getAttributes() {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = array();
        foreach ($props as $k => $p) {
            if (!$p->isStatic()) {
                $name = $p->getName();
                $result[$name] = $this->$name;
            }
        }
        return $result;
    }

    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

    public function getDefaultFields() {
        $fields = $this->attributes;
        $exclude = array();
        $array = array();
        foreach ($fields as $k => $f) {
            $array[] = array(
                'name' => $k,
                'type' => 'TextField',
                'label' => ucfirst($k)
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

}
