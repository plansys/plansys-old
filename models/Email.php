<?php

class Email extends ActiveRecord {

    public function rules() {
        return array(
            array('user_id, subject', 'required'),
            array('user_id', 'numerical', 'integerOnly' => true),
        );
    }

    public static function send($user_id, $subject, $content, $template = '') {
        
    }

    public function relations() {
        return array(
            'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    public function tableName() {
        return 'p_email_queue';
    }

}