<?php

class User extends ActiveRecord {
 
    public function attributeLabels() {
        return array(
            'id'=> 'ID',
            'nip'=> 'Nip',
            'fullname'=> 'Fullname',
            'email'=> 'Email',
            'phone'=> 'Phone',
            'username'=> 'Username',
            'password'=> 'Password',
            'date'=> 'Date',
        );
    }

    public function rules() {
        return array(
            array('nip, fullname, email, username, password, date', 'required'),
            array('nip, fullname, email, phone, username, password', 'length', 'max'=>255),
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