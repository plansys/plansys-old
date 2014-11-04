<?php

class Role extends ActiveRecord {

    public function rules() {
        return array(
            array('role_name, role_description', 'required'),
            array('menu_path, home_url', 'safe'),
            array('role_name, role_description', 'length', 'max' => 255),
        );
    }

    public function relations() {
        return array(
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'role_id'),
            'users' => array(self::HAS_MANY, 'User', array('user_id' => 'id'), 'through' => 'userRoles'),
        );
    }
    
    public function getName() {
        return $this->role_name;
    }
    
    public static function getAll() {
        $all = Role::model()->findAll();
        $result = array();
        foreach ($all as $k=>$v) {
            $result[$v->id] = $v->role_description;
        }
        return $result;
    }
    
    private $oldName = "";
    public function afterFind() {
        $this->oldName = $this->role_name;
    }
    
    public function afterSave() {
        parent::afterSave();
        
        
        $sql = "UPDATE p_nfy_subscription_categories "
            . "set category = 'role_{$this->role_name}.' "
            . "where category = 'role_{$this->oldName}.';";
        Yii::app()->db->createCommand($sql)->execute();
        return true;
    }
    
    public static function listRole() {
        $list = CHtml::listData(Role::model()->findAll(), 'id', 'role_description');
        return $list;
    }

    public function tableName() {
        return 'p_role';
    }

}
