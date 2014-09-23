<?php

class Todo extends ActiveRecord {

    const ACTIVE = 0;
    const DONE = 1;

    public function rules() {
        return array(
            array('note, user_id', 'required'),
            array('user_id, status', 'numerical', 'integerOnly' => true),
            array('type, options, status', 'safe')
        );
    }

    public function relations() {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function tableName() {
        return 'p_todo';
    }

}
