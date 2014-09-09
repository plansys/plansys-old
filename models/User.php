<?php

class User extends ActiveRecord {


    public function rules() {
        return array(
            array('nip, fullname, email, phone, username, password', 'required'),
            array('nip, fullname, email, username, password', 'length', 'max' => 255),
            array('last_login', 'safe')
        );
    }

    public function relations() {
        return array(
            'userInfos' => array(self::HAS_MANY, 'UserInfo', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id'),
        );
    }

    public function tableName() {
        return 'p_user';
    }

}