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

}
