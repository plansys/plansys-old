<?php

class Role extends ActiveRecord {
 
    public function rules() {
        return array(
            array('role_name, role_description, parent_id', 'required'),
            array('parent_id', 'numerical', 'integerOnly'=>true),
            array('role_name, role_description', 'length', 'max'=>255),
        );
    }

    public function relations() {
        return array(
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'role_id'),
        );
    }

    public static function listRole() {
        return CHtml::listData(Role::model()->findAll(), 'role_id', 'role_description');
    }
    
    public function tableName() {
        return 'p_role';
    }

}