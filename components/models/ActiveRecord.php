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

    public function getModelFieldList() {
        $fields = array_keys($this->attributes);

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

    public function getDefaultFields() {
        $array = $this->modelFieldList;
        $column2 = array(array_shift($array));
        $column1 = $array;

        $return = array(
            array(
                'type' => 'ActionBar',
            ),
            array(
                'type' => 'ColumnField',
                'column1' => $column1,
                'column2' => $column2
            )
        );
        return $return;
    }

}
