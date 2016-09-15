<?php

class Role extends ActiveRecord {

    public function rules() {
        return array(
            array('role_name, role_description', 'required'),
            array('role_name', 'unique'),
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
        foreach ($all as $k => $v) {
            $result[$v->id] = $v->role_description;
        }
        return $result;
    }

    private $oldName = "";

    public function getRootRole() {
        return Helper::explodeFirst(".", $this->role_name);
    }

    public function beforeSave() {
        if ($this->isNewRecord && $this->repo_path == '') {
            $role = explode(".", $this->role_name);
            $this->repo_path = array_shift($role);
        }
        return true;
    }
    
    public function afterFind() {
        $this->oldName = $this->role_name;

        return true;
    }

    public function afterSave() {
        parent::afterSave();
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