<?php

class UserRole extends ActiveRecord {
 
    public function rules() {
        return array(
            array('user_id, role_id', 'required'),
            array('user_id, role_id', 'numerical', 'integerOnly'=>true),
            array('is_default_role', 'length', 'max'=>3),
        );
    }

    public function relations() {
        return array(
            'role' => array(self::BELONGS_TO, 'Role', 'role_id'),
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function tableName() {
        return 'p_user_role';
    }

}