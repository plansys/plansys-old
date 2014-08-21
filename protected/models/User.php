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
        );
    }

    public function tableName() {
        return 'p_user';
    }

    public function rules() {
        return array(
            array('nip, firstname, lastname, email, username, password', 'required'),
            array('nip, firstname, lastname, email, phone, username, password', 'length', 'max'=>255),
        );
    }

    public function relations() {
        return array(

        );
    }

}