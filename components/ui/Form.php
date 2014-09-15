<?php
/**
 * Class Form
 * @author rizky
 */
class Form extends CFormModel {
    
    /**
     * getAttributes
     * Fungsi ini digunakan untuk mendapatkan attributes field dan me-returnnya
     * @return array me-return array atribut field
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
     * @param array $values parameter sebuah array atribut field
     */
    public function setAttributes($values) {
        foreach ($values as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
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

    /**
     * @return array me-return array property DefaultFields.
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
        return $array;
    }

}
