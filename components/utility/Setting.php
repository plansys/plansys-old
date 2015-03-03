<?php

class Setting {

    private static $data;
    public static $basePath;
    public static $rootPath;
    public static $path        = "";
    public static $default     = [
        'db'   => [
            'driver'   => 'mysql',
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => ''
        ],
        'repo' => [
            'path' => 'repo'
        ],
        'app'  => [
            'dir'  => 'app',
            'mode' => 'development'
        ],
    ];
    public static $mode        = null;
    public static $entryScript = "";

    private static function setupBasePath($configFile) {
        $configFile = str_replace("/", DIRECTORY_SEPARATOR, $configFile);
        $basePath   = dirname($configFile);
        $basePath   = explode(DIRECTORY_SEPARATOR, $basePath);

        array_pop($basePath);
        Setting::$basePath = implode(DIRECTORY_SEPARATOR, $basePath);

        array_pop($basePath);
        Setting::$rootPath = implode(DIRECTORY_SEPARATOR, $basePath);

        return Setting::$basePath;
    }

    public static function fullPath() {
        $s        = &$_SERVER;
        $ssl      = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp       = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port     = $s['SERVER_PORT'];
        $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host     = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host     = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        $uri      = $protocol . '://' . $host . $s['REQUEST_URI'];
        $segments = explode('?', $uri, 2);
        $url      = $segments[0];
        return $url;
    }

    public static function getLDAP() {
        $ldap = Setting::get('ldap');

        if (!is_null($ldap)) {
            return [
                'class'   => 'application.extensions.adLDAP.YiiLDAP',
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

    public static function init($configfile, $mode = "running", $entryScript = "") {
        require_once("Installer.php");

        date_default_timezone_set("Asia/Jakarta");
        $bp            = Setting::setupBasePath($configfile);
        Setting::$path = $bp . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "settings.json";

        if (!is_file(Setting::$path)) {
            $json   = Setting::$default;
            $json   = json_encode($json, JSON_PRETTY_PRINT);
            $result = @file_put_contents(Setting::$path, $json);
            
            require_once("Installer.php");
            Installer::createIndexFile("install");
            Setting::$mode = "install";
        }
        $file = @file_get_contents(Setting::$path);

        ## set entry script
        Setting::$entryScript = realpath($entryScript == "" ? $_SERVER["SCRIPT_FILENAME"] : $entryScript);

        ## set default data value
        if (!$file || (isset($result) && !$result)) {
            Setting::$data = Setting::$default;

            Setting::redirError("Failed to write in '{path}'", [
                "path" => (isset($result) && !$result) ? $result : $file
            ]);
        } else {
            $setting       = json_decode($file, true);
            Setting::$data = Setting::arrayMergeRecursiveReplace(Setting::$default, $setting);
        }


        ## set host
        if (!Setting::get('app.host')) {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $port     = $_SERVER['SERVER_PORT'] == 443 || $_SERVER['SERVER_PORT'] == 80 ? "" : ":" . $_SERVER['SERVER_PORT'];
            Setting::set('app.host', $protocol . $_SERVER['HTTP_HOST'] . $port);
        }


        ## set debug
        if (Setting::$mode == null) {
            Setting::$mode = $mode;
        }

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
        while ($k   = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        if ($arr == null) {
            $arr = $default;
        }

        return $arr;
    }

    public static function redirError($msg, $params = array()) {

        if (@$_GET['r'] != "install/default/index") {
            header("Location: " . Setting::fullPath() . "?r=install/default/index");
            die();
        }

        $_GET['msg'] = Setting::t($msg, $params);
    }

    function getPreferredLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ($n = preg_match_all('/([\w\-]+)\s*(;\s*q\s*=\s*(\d*\.\d*))?/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches)) > 0) {
            $languages                  = array();
            for ($i = 0; $i < $n; ++$i)
                $languages[$matches[1][$i]] = empty($matches[3][$i]) ? 1.0 : floatval($matches[3][$i]);
            arsort($languages);
            foreach ($languages as $language => $pref) {
                $lang     = strtolower(str_replace('-', '_', $language));
                if (preg_match("/^en\_?/", $lang))
                    return false;
                if (!is_file($viewFile = dirname(__FILE__) . "/views/$lang/index.php"))
                    $lang     = false;
                else
                    break;
            }
            return $lang;
        }
        return false;
    }

    function t($message, $params = array()) {
        static $messages;

        if ($messages === null) {
            $messages = array();
            if (($lang     = Setting::getPreferredLanguage()) !== false) {
                $file = dirname(__FILE__) . "/messages/$lang/yii.php";
                if (is_file($file)) {
                    $messages = include($file);
                }
            }
        }
        if (empty($message)) {
            return $message;
        }
        if (isset($messages[$message]) && $messages[$message] !== '') {
            $message = $messages[$message];
        }
        return $params !== array() ? strtr($message, $params) : $message;
    }

    public static function write() {
        $result = @file_put_contents(Setting::$path, json_encode(Setting::$data, JSON_PRETTY_PRINT));
    }

    public static function set($key, $value, $flushSetting = true) {
        Setting::setInternal(Setting::$data, $key, $value);

        if ($flushSetting) {
            Setting::write();
        }
    }

    private static function setInternal(&$arr, $path, $value) {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }

    public static function checkPath($path, $writable = false) {
        if (!is_dir($path)) {
            if (!@mkdir($path, 0775)) {
                $error   = error_get_last();
                $message = Setting::t("Failed to create directory <br/>'{path}'<br/>because: {error}");
                $message = strtr($message, [
                    '{path}'  => $path,
                    '{error}' => $error['message']
                ]);

                return $message;
            }
        }

        if ($writable) {
            if (!is_writable($path)) {
                $message = Setting::t("Failed to write in <br/>'{path}'<br/>because: {error}");
                $message = strtr($message, [
                    '{path}'  => $path,
                    '{error}' => 'Permission Denied'
                ]);

                return $message;
            }
        }
        return true;
    }

    public static function finalizeConfig($config, $type = "main") {
        ## check if plansys is installed or not
        if (Setting::$mode == "init" || Setting::$mode == "install") {
            require_once("Installer.php");
            $config = Installer::init($config);
        } else {
            $config['components']['curl'] = array(
                'class'   => 'ext.curl.Curl',
                'options' => array(CURLOPT_HEADER => true),
            );

            if ($type == "main" && Setting::getThemePath() != "") {
                $config['components']['themeManager'] = array(
                    'basePath' => Setting::getThemePath()
                );
                $config['theme']                      = 'default';
            }
        }


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

    public static function getConfigPath() {
        return Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "config";
    }

    public static function getAssetPath() {
        return Setting::getRootPath() . DIRECTORY_SEPARATOR . "assets";
    }

    public static function getCommandMap($modules = null) {
        $commands = [];
        $modules  = is_null($modules) ? Setting::getModules() : $modules;

        foreach ($modules as $m) {
            $moduleClass = explode(".", $m['class']);
            array_pop($moduleClass);
            $moduleName  = array_pop($moduleClass);
            array_push($moduleClass, $moduleName);
            $modulePath  = implode(".", $moduleClass);

            $path = Yii::getPathOfAlias($modulePath . ".commands");
            if (is_dir($path)) {
                $cmds = glob($path . DIRECTORY_SEPARATOR . "*.php");
                foreach ($cmds as $c) {
                    $dir  = explode(DIRECTORY_SEPARATOR, $c);
                    $file = array_pop($dir);
                    $cmd  = lcfirst(str_replace("Command.php", "", $file));

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
                $ctrl  = lcfirst(str_replace("Controller", "", $class));

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
        $modules    = glob(Setting::getBasePath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");
        $appModules = glob(Setting::getAppPath() . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "*");

        $return = [];
        foreach ($modules as $key => $module) {
            $m          = Setting::explodeLast(DIRECTORY_SEPARATOR, $module);
            $return[$m] = [
                'class' => 'application.modules.' . $m . '.' . ucfirst($m) . 'Module'
            ];
        }

        foreach ($appModules as $key => $module) {
            $m = Setting::explodeLast(DIRECTORY_SEPARATOR, $module);

            if (!is_file(ucfirst($m) . 'Module.php'))
                continue;

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
                'emulatePrepare'   => true,
                'username'         => Setting::get('db.username'),
                'password'         => Setting::get('db.password'),
                'charset'          => 'utf8',
            ];
        } else {
            $connection = [
                'connectionString' => Setting::get('db.driver') . ':host=' . Setting::get('db.server') . ';port=' . Setting::get('db.port') . ';dbname=' . Setting::get('db.dbname'),
                'emulatePrepare'   => true,
                'username'         => Setting::get('db.username'),
                'password'         => Setting::get('db.password'),
                'charset'          => 'utf8',
            ];
        }
        return $connection;
    }

    public static function getDBDriverList() {
        return [
            'mysql' => 'MySQL',
//                  'pgsql' => 'PostgreSQL',
//                  'sqlsrv' => 'SQL Server',
//                  'sqlite' => 'SQLite',
//                  'oci' => 'Oracle'
        ];
    }

}
