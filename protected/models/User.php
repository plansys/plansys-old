<?php

class User extends ActiveRecord {
 
    public function attributeLabels() {
        return array(
            'id'=> 'ID',
            'nip'=> 'Nip',
            'firstname'=> 'Firstname',
            'lastname'=> 'Lastname',
            'email'=> 'Email',
            'phone'=> 'Phone',
            'username'=> 'Username',
            'password'=> 'Password',
            'date'=> 'Date',
        );
    }

    public function tableName() {
        return 'p_user';
    }

    public function rules() {
        return array(
            array('nip, firstname, lastname, email, username, password, date', 'required'),
            array('nip, firstname, lastname, email, phone, username, password', 'length', 'max'=>255),
        );
    }

    public function relations() {
        return array(
            'userInfos' => array(self::HAS_MANY, 'UserInfo', 'user_id'),
            'userRoles' => array(self::HAS_MANY, 'UserRole', 'user_id'),
        );
    }

}