<?php
/**
 * Class Form
 * @author rizky
 */
class Form extends CComponent {
	/** 
	 * @var array $_errors
	 * @access private	
	*/
    private $_errors = array();
	
	/**
	 * @return array Fungsi ini digunakan untuk mendapatkan error yang dialami. fungsi ini akan me-return array $_errors yang didalamnya menampung error yang terjadi.
	*/
    public function getErrors() {
        return $this->_errors;
    }
    
	/**
	 * @param string $value
	 * @return null Fungsi ini akan men-set error yang ditampung pada $value kedalam variable $_errors.
	*/
    public function setErrors($value) {
        $this->_errors = $value;
    }
    
	/**
	 * @return array Fungsi ini digunakan untuk mendapatkan attributes field dan me-returnnya.
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
	 * @param array $values
	 * @return null Fungsi ini berfungsi untuk men-set attributes field dengan property-property pada parameter $values.
	*/
    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

	/**
	 * @return array Fungsi ini akan me-return attributes dari form.
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
            'value' => '<h2><center>{{ form.title }}</center></h2><hr/>'
        ));
        array_push($array, array(
            'label' => 'Submit',
            'type' => 'SubmitButton',
        ));
        return $array;
    }

}
