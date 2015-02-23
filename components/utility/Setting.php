<?php

class Setting {

    private static $data;
    public static $basePath;
    public static $rootPath;
    public static $path = "";
    public static $default = [
        'db' => [
            'driver' => 'mysql',
            'server' => '',
            'username' => '',
            'password' => '',
            'dbname' => ''
        ],
        'repo' => [
            'path' => 'repo'
        ],
        'app' => [
            'dir' => 'app',
            'mode' => 'development'
        ],
    ];
    private static $_isInstalled = null;

    private static function setupBasePath($configFile) {
        $configFile = str_replace("/", DIRECTORY_SEPARATOR, $configFile);
        $basePath = dirname($configFile);
        $basePath = explode(DIRECTORY_SEPARATOR, $basePath);

        array_pop($basePath);
        Setting::$basePath = implode(DIRECTORY_SEPARATOR, $basePath);

        array_pop($basePath);
        Setting::$rootPath = implode(DIRECTORY_SEPARATOR, $basePath);

        return Setting::$basePath;
    }

    public static function getLDAP() {
        $ldap = Setting::get('ldap');

        if (!is_null($ldap)) {
            return [
                'class' => 'application.extensions.adLDAP.YiiLDAP',
                'options' => $ldap
            ];
        } else {
            return [];
        }
    }

    private static function arrayMergeRecursiveReplace($paArray1, $paArray2) {
        if (!is_array($paArray1) or ! is_array($paArray2)) {
            return $paArray2;
        }
        foreach ($paArray2 AS $sKey2 => $sValue2) {
            $paArray1[$sKey2] = Setting::arrayMergeRecursiveReplace(@$paArray1[$sKey2], $sValue2);
        }
        return $paArray1;
    }

    public static function init($configfile, $installed = null) {
        date_default_timezone_set("Asia/Jakarta");
        $bp = Setting::setupBasePath($configfile);
        Setting::$path = $bp . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.json";

        if (!is_file(Setting::$path)) {
            $json = Setting::$default;
            $json = json_encode($json, JSON_PRETTY_PRINT);
            file_put_contents(Setting::$path, $json);
        }

        ## set default data value
        $setting = json_decode(file_get_contents(Setting::$path), true);
        Setting::$data = Setting::arrayMergeRecursiveReplace(Setting::$default, $setting);

        if (!Setting::$data) {
            echo "Failed to load [" . Setting::$path . "], invalid json file!";
            die();
        }

        ## set host
        if (!Setting::get('app.host')) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $port = $_SERVER['SERVER_PORT'] == 443 || $_SERVER['SERVER_PORT'] == 80 ? "" : ":" . $_SERVER['SERVER_PORT'];
            Setting::set('app.host', $protocol . $_SERVER['HTTP_HOST'] . $port);
        }

        ## set debug
        Setting::$_isInstalled = $installed;

        if (Setting::get('app.mode') != 'production') {
            defined('YII_DEBUG') or define('YII_DEBUG', true);
            defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
        }
    }

    public static function initPath() {
        Yii::setPathOfAlias('app', Setting::getAppPath());
        Yii::setPathOfAlias('application', Setting::getApplicationPath());
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

    public static function checkPath($path) {
        if (!is_dir($path)) {
            if (!@mkdir($path)) {
                $error = error_get_last();
                $message = Yii::t('plansys', "Failed to create directory '{path}' because : {error}");
                $message = strtr($message, [
                    '{path}' => $path,
                    '{error}' => $error['message']
                ]);

                return $message;
            }
        }
        return true;
    }

    public static function isInstalled() {
        require_once("Installer.php");
        if (Setting::$_isInstalled !== true) {
            return Installer::checkInstall();
        } else {
            return Installer::checkInstall("Checking Directory Permission");
            return true;
        }
    }

    public static function finalizeConfig($config) {
        ## check if plansys is installed or not
        if (!Setting::isInstalled()) {
            $config = Installer::init($config);
        } else {
            $config['components']['curl'] = array(
                'class' => 'ext.curl.Curl',
                'options' => array(CURLOPT_HEADER => true),
            );

            if (Setting::getThemePath() != "") {
                $config['components']['themeManager'] = array(
                    'basePath' => Setting::getThemePath()
                );
            }
            $config['theme'] = 'default';
        }
        ## return config
        return $config;
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

    public static function getApplicationPath() {
        return Setting::$rootPath . DIRECTORY_SEPARATOR . 'plansys';
    }

    public static function getPlansysDirName() {
        return Helper::explodeLast(DIRECTORY_SEPARATOR, Yii::getPathOfAlias('application'));
    }

    public static function getThemePath() {
        $themePath = Yii::getPathOfAlias(Setting::get('app.dir')) . DIRECTORY_SEPARATOR . "themes";

        if (is_dir($themePath)) {
            return Setting::get('app.dir') . "/themes";
        }
        return "";
    }

    public static function getModulePath() {
        if (file_exists(Yii::getPathOfAlias('app.modules'))) {
            return Yii::getPathOfAlias('app.modules');
        } else {
            return Yii::getPathOfAlias('application.modules');
        }
    }

    public static function getRuntimePath() {
        return Setting::getRootPath() . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "runtime";
    }

    public static function getAssetPath() {
        return Setting::getRootPath() . DIRECTORY_SEPARATOR . "assets";
    }

    public static function getCommandMap($modules = null) {
        $commands = [];
        $modules = is_null($modules) ? Setting::getModules() : $modules;

        foreach ($modules as $m) {
            $moduleClass = explode(".", $m['class']);
            array_pop($moduleClass);
            $moduleName = array_pop($moduleClass);
            array_push($moduleClass, $moduleName);
            $modulePath = implode(".", $moduleClass);

            $path = Yii::getPathOfAlias($modulePath . ".commands");
            if (is_dir($path)) {
                $cmds = glob($path . DIRECTORY_SEPARATOR . "*.php");
                foreach ($cmds as $c) {
                    $dir = explode(DIRECTORY_SEPARATOR, $c);
                    $file = array_pop($dir);
                    $cmd = lcfirst(str_replace("Command.php", "", $file));

                    $commands[$moduleName . "." . $cmd] = [
                        'class' => Helper::getAlias($c)
                    ];
                }
            }
        }

        return $commands;
    }

    public static function getControllerMap() {
        $controllers = [];

        ## get site controller
        if (is_dir(Yii::getPathOfAlias('app.controllers'))) {
            $gls = glob(Yii::getPathOfAlias('app.controllers') . DIRECTORY_SEPARATOR . "*.php");
            foreach ($gls as $g) {
                $class = str_replace(".php", "", basename($g));
                $ctrl = lcfirst(str_replace("Controller", "", $class));

                if (substr($class, 0, 3) == "App") {
                    $extendClass = substr($class, 3);
                    Yii::import('application.controllers.' . $extendClass);

                    $ctrl = lcfirst(substr($ctrl, 3));
                }

                $controllers[$ctrl] = 'app.controllers.' . $class;
            }
        }

        return $controllers;
    }

    public static function explodeLast($delimeter, $str) {
        $a = explode($delimeter, $str);
        return end($a);
    }

    public static function getModules() {
        $modules = glob(Setting::getBasePath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");
        $appModules = glob(Setting::getAppPath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");

        $return = [];
        foreach ($modules as $key => $module) {
            $m = Setting::explodeLast(DIRECTORY_SEPARATOR, $module);
            $return[$m] = [
                'class' => 'application.modules.' . $m . '.' . ucfirst($m) . 'Module'
            ];
        }

        foreach ($appModules as $key => $module) {
            $m = Setting::explodeLast(DIRECTORY_SEPARATOR, $module);
            $return[$m] = [
                'class' => 'app.modules.' . $m . '.' . ucfirst($m) . 'Module'
            ];
        }
        return $return;
    }

    public static function getDB() {
        if (Setting::get('db.port') == null) {
            $connection = [
                'connectionString' => Setting::get('db.driver') . ':host=' . Setting::get('db.server') . ';dbname=' . Setting::get('db.dbname'),
                'emulatePrepare' => true,
                'username' => Setting::get('db.username'),
                'password' => Setting::get('db.password'),
                'charset' => 'utf8',
            ];
        } else {
            $connection = [
                'connectionString' => Setting::get('db.driver') . ':host=' . Setting::get('db.server') . ';port=' . Setting::get('db.port') . ';dbname=' . Setting::get('db.dbname'),
                'emulatePrepare' => true,
                'username' => Setting::get('db.username'),
                'password' => Setting::get('db.password'),
                'charset' => 'utf8',
            ];
        }
        return $connection;
    }

    public static function getDBDriverList() {
        return [
            'mysql' => 'MySQL',
            /*
              'pgsql' => 'PostgreSQL',
              'sqlsrv' => 'SQL Server',
              'sqlite' => 'SQLite',
              'oci' => 'Oracle'
             *
             */
        ];
    }

}
