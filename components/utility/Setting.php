<?php

class Setting {

    private static $data;
    public static $basePath;
    public static $rootPath;
    public static $path = "";
    public static $default = array(
        'db' => array(
            'driver' => 'mysql',
            'server' => '',
            'username' => '',
            'password' => '',
            'dbname' => ''
        ),
        'repo' => array(
            'path' => 'repo'
        ),
        'app' => array(
            'dir' => 'app'
        ),
    );

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
        date_default_timezone_set("Asia/Jakarta");
        $bp = Setting::setupBasePath($configfile);
        Setting::$path = $bp . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.json";

        if (!is_file(Setting::$path)) {
            $json = Setting::$default;
            $json = json_encode($json, JSON_PRETTY_PRINT);

            file_put_contents(Setting::$path, $json);
        }
        Setting::$data = json_decode(file_get_contents(Setting::$path), true);

        Yii::setPathOfAlias('app', Setting::getAppPath());
        Yii::setPathOfAlias('repo', Setting::get('repo.path'));
    }

    public static function get($key, $default = null) {
        $keys = explode('.', $key);

        $arr = Setting::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }
        
        if ($arr == null) {
            $arr = $default;
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

    public static function getAppPath() {
        return Setting::$rootPath . DIRECTORY_SEPARATOR . Setting::get('app.dir');
    }
    
    public static function getPlansysDirName() {
        return array_pop(explode(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application')));
    }

    public static function getModulePath() {
        if (file_exists(Yii::getPathOfAlias('app.modules'))) {
            return Yii::getPathOfAlias('app.modules');
        } else {
            return Yii::getPathOfAlias('application.modules');
        }
    }

    public static function getModules() {
        $modules = glob(Setting::getBasePath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");
        $appModules = glob(Setting::getAppPath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");

        $return = array();
        foreach ($modules as $key => $module) {
            $m = array_pop(explode(DIRECTORY_SEPARATOR, $module));
            $return[$m] = array(
                'class' => 'application.modules.' . $m . '.' . ucfirst($m) . 'Module'
            );
        }

        foreach ($appModules as $key => $module) {
            $m = array_pop(explode(DIRECTORY_SEPARATOR, $module));
            $return[$m] = array(
                'class' => 'app.modules.' . $m . '.' . ucfirst($m) . 'Module'
            );
        }
        return $return;
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
