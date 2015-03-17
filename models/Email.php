<?php

class Email extends ActiveRecord {
    public static $path = "";
    public static $setting;
    public static $errorLog;
    public static $data;
    
    public function rules() {
        return array(
            array('user_id, subject', 'required'),
            array('user_id', 'numerical', 'integerOnly' => true),
        );
    }
    
    public static function initalSetting($resetSetting = false, $resetErrorLog = false){
        Email::$path = Setting::getRuntimePath().DIRECTORY_SEPARATOR."email";
        if(!file_exists(Email::$path)){
            mkdir(Email::$path, 0777);
        }
        
        Email::$setting = Email::$path.DIRECTORY_SEPARATOR."setting.json";
        if(!file_exists(Email::$setting)){
            touch(Email::$setting);
            Email::$data = ['email' => Setting::get("email")];
            $result = @file_put_contents(Email::$setting, json_encode(Email::$data, JSON_PRETTY_PRINT));
        }
        $file = @file_get_contents(Email::$setting);
        
        if($resetSetting){
            Email::$data = ['email' => Setting::get("email")];
            $result = @file_put_contents(Email::$setting, json_encode(Email::$data, JSON_PRETTY_PRINT));
        }else{
            $setting = json_decode($file, true);
            Email::$data = $setting;
        }
        
        Email::$errorLog = Email::$path.DIRECTORY_SEPARATOR."error.log";
        if(!file_exists(Email::$errorLog)){
            touch(Email::$errorLog);
        }
        
        if($resetErrorLog){
            $fh = fopen(Email::$errorLog , 'w' );
            fclose($fh);
        }
    }
    
    public static function remove($key) {
        Email::initalSetting();
        $keys = explode('.', $key);

        $arr = &Email::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
            $length = count($keys);

            if ($length == 1) {
                unset($arr[$keys[0]]);
                break;
            }
        }

        $result = @file_put_contents(Email::$setting, json_encode(Email::$data, JSON_PRETTY_PRINT));
    }
    
    public static function get($key, $default = null) {
        Email::initalSetting();
        
        $keys = explode('.', $key);

        $arr = Email::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        if ($arr == null) {
            $arr = $default;
        }

        return $arr;
    }
    
    public static function set($path, $value) {
        Email::initalSetting();
        
        $keys = explode('.', $path);
        
        $arr = &Email::$data;
        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;

        $result = @file_put_contents(Email::$setting, json_encode(Email::$data, JSON_PRETTY_PRINT));
    }

    public static function sendTestMail() {
        Email::initalSetting(false,true);
        NodeProcess::start('plansys/commands/shell/tes.mail.js', Email::$path);
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