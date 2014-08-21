<?php

class Form extends CComponent {
	/** 
	 * @var array variable untuk menampung error.
	 * @access private	
	*/
    private $_errors = array();
	
	/**
	 * @return array Fungsi ini berfungsi untuk memdapatkan error yang dialami dan kemudian men-return _errors.
	*/
    public function getErrors() {
        return $this->_errors;
    }
    
	/**
	 * @param string $value Parameter untuk melempar value error.
	 * @return null Fungsi ini akan men-set error yang ditampung pada $value kedalam _errors. $value disini adalah parameter.
	*/
    public function setErrors($value) {
        $this->_errors = $value;
    }
    
	/**
	 * @return array Fungsi ini berfungsi untuk memdapatkan attributes form.
	*/
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

	/**
	 * @param array $values Parameter untuk melempar value attributes form.
	 * @return null Fungsi ini berfungsi untuk men-set attributes form dengan parameter $values yang berupa array.
	*/
    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

	/**
	 * @return array Fungsi ini akan me-return attributes form.
	*/
    public static function attributes() {
        $field = new static();
        return $field->attributes;
    }

	/**
	 * @return array Fungsi ini akan me-return array property DefaultFields.
	 */
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
