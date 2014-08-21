<?php

class Setting {

    private static $data;
    public static $basePath;
    public static $path = "";
    public static $default = array(
        'app' => array(
            'name' => 'Plansys'
        ),
        'db' => array(
            'driver' => 'mysql',
            'server' => '',
            'username' => '',
            'password' => '',
            'dbname'
        )
    ); 
    
    private static function setupBasePath($configfile) {
        $basePath = dirname($configfile);
        $basePath = explode(DIRECTORY_SEPARATOR, $basePath);
        array_pop($basePath);
        $basePath = implode(DIRECTORY_SEPARATOR, $basePath);
        Setting::$basePath = $basePath;
        return $basePath;
    }

    public static function init($configfile) {
        $bp = Setting::setupBasePath($configfile);
        Setting::$path = $bp . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.json";

        if (!is_file(Setting::$path)) {
            $json = json_encode(Setting::$default, JSON_PRETTY_PRINT);
            
            file_put_contents(Setting::$path, $json);
        }
        Setting::$data = json_decode(file_get_contents(Setting::$path), true);
    }

    public static function get($key) {
        $keys = explode('.', $key);

        $arr = Setting::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        return $arr;
    }

    public static function set($key, $value) {
        Setting::setInternal(Setting::$data, $key, $value);
        file_put_contents(Setting::$path, json_encode(Setting::$data, JSON_PRETTY_PRINT));
    }

    private static function setInternal(&$arr, $path, $value) {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    public static function getBasePath() {
        return Setting::$basePath;
    }

    public static function getDoctrineDB() {
        return array(
            'driver' => 'pdo_mysql',
            'dbname' => 'plansys',
            'user' => 'root',
            'password' => 'okedeh'
        );
    }

    public static function getDB() {
        return array(
            'connectionString' => 'mysql:host=localhost;dbname=plansys',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'okedeh',
            'charset' => 'utf8',
        );
    }

}
