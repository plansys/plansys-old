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
    public static function batchUpdate($model,$data){
        $table = $model::model()->tableSchema->name;
        $update = "";
        foreach ($data as $d){
            $d['id'] = $cond;
            unset($d['id']);
            $field = array_keys($d);
            $columns = count($field);
            $update .= "UPDATE ".$table." SET";
        }
    }
    public static function batchInsert($model, $data){
        $table = $model::model()->tableSchema->name;
        $builder=Yii::app()->db->schema->commandBuilder;
        $command=$builder->createMultipleInsertCommand($table, $data);
        $command->execute();
    }

    public function getDefaultFields() {
        $array = $this->modelFieldList;
        $length = count($array);
        $column1 = array();
        $column2 = array();
        $array_id = null;

        foreach ($array as $k => $i) {
            if ($array[$k]['name'] == 'id') {
                $array_id = $array[$k];
                continue;
            }

            if ($k < $length / 2) {
                $column1[] = $array[$k];
            } else {
                $column2[] = $array[$k];
            }
        }


        $return = array();
        $return[] = array(
            'type' => 'ActionBar',
        );

        if (!is_null($array_id)) {
            $return[] = $array_id;
        }

        $return[] = array(
            'type' => 'ColumnField',
            'column1' => $column1,
            'column2' => $column2
            )
        ;
        return $return;
    }

}
