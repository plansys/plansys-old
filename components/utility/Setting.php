<?php

class Setting {

    private static $data;
    public static $basePath;
    public static $rootPath;
    public static $path = "";
    public static $default = '{"db":{"driver":"mysql","server":"","username":"","password":"","dbname":""}}';

    private static function setupBasePath($configfile) {
        $basePath = dirname($configfile);
        $basePath = explode(DIRECTORY_SEPARATOR, $basePath);
        
        array_pop($basePath);
        Setting::$basePath = implode(DIRECTORY_SEPARATOR, $basePath);
        
        array_pop($basePath);
        Setting::$rootPath = implode(DIRECTORY_SEPARATOR, $basePath);
        
        return Setting::$basePath;
    }

    public static function init($configfile) {
        $bp = Setting::setupBasePath($configfile);
        Setting::$path = $bp . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.json";

        if (!is_file(Setting::$path)) {
            $json = json_decode(Setting::$default);
            $json = json_encode($json, JSON_PRETTY_PRINT);

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
    
    public static function getRootPath() {
        return Setting::$rootPath;
    }

    public static function getDoctrineDB() {
        $connection = array(
            'driver' => 'pdo_' . Setting::get('db.driver'),
            'port' => Setting::get('db.port'),
            'dbname' => Setting::get('db.dbname'),
            'user' => Setting::get('db.username'),
            'password' => Setting::get('db.password')
        );
        return $connection;
    }

    public static function getDB() {
        if (Setting::get('db.port') == null) {
            $connection = array(
                'connectionString' => Setting::get('db.driver') . ':host=' . Setting::get('db.server') . ';dbname=' . Setting::get('db.dbname'),
                'emulatePrepare' => true,
                'username' => Setting::get('db.username'),
                'password' => Setting::get('db.password'),
                'charset' => 'utf8',
            );
        } else {
            $connection = array(
                'connectionString' => Setting::get('db.driver') . ':host=' . Setting::get('db.server') . ';port=' . Setting::get('db.port') . ';dbname=' . Setting::get('db.dbname'),
                'emulatePrepare' => true,
                'username' => Setting::get('db.username'),
                'password' => Setting::get('db.password'),
                'charset' => 'utf8',
            );
        }
        return $connection;
    }

    public static function getDBDriverList() {
        return array(
            'mysql' => 'MySQL',
                /*
                  'pgsql' => 'PostgreSQL',
                  'sqlsrv' => 'SQL Server',
                  'sqlite' => 'SQLite',
                  'oci' => 'Oracle'
                 * 
                 */
        );
    }

}
