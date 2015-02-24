<?php

class Installer {

    private static $_errorList = [];

    public static function getErrorList() {
        return Installer::$_errorList;
    }

    public static function setError($group, $idx, $error) {
        if (isset(Installer::$_errorList[$group])) {
            Installer::$_errorList[$group] = [];
        }
        Installer::$_errorList[$group][$idx] = $error;
    }

    public static function getError($group, $idx = -1) {
        if (!isset(Installer::$_errorList[$group])) {
            return false;
        } else {
            if ($idx == -1) {
                return true;
            } else if (isset(Installer::$_errorList[$group][$idx])) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function checkServerVar() {

        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var]))
                $missing[] = $var;
        }
        if (!empty($missing))
            return t('yii', '$_SERVER does not have {vars}.', array('{vars}' => implode(', ', $missing)));

        if (realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(__FILE__))
            return t('yii', '$_SERVER["SCRIPT_FILENAME"] must be the same as the entry script file path.');

        if (!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"]))
            return t('yii', 'Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');

        if (!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0)
            return t('yii', 'Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');

        return '';
    }

    public static function getCheckList($checkGroup = "") {
        $checkLists = [
            "Checking Directory Permission" => [
                [
                    "title" => "Checking base directory permissions",
                    "check" => function() {
                        return Setting::checkPath(Setting::getBasePath());
                    }
                ],
                [
                    "title" => "Checking app directory permissions",
                    "check" => function() {
                        return Setting::checkPath(Setting::getAppPath());
                    }
                ],
                [
                    "title" => "Checking assets directory permissions",
                    "check" => function() {
                        return Setting::checkPath(Setting::getAssetPath());
                    }
                ],
                [
                    "title" => "Checking runtime directory permissions",
                    "check" => function() {
                        return Setting::checkPath(Setting::getRuntimePath());
                    }
                ],
                [
                    "title" => "Checking repository directory permissions",
                    "check" => function() {
                        return Setting::checkPath(Setting::get('repo.path'));
                    }
                ]
            ],
            "Checking Framework Requirements" => [
                [
                    'title' => 'Checking PHP Version ( > 5.5.0 )',
                    'check' => function() {
                        return version_compare(PHP_VERSION, "5.5.0", ">");
                    }
                ],
                [
                    'title' => 'Reflection Extension',
                    'check' => function() {
                        return class_exists('Reflection', false);
                    }
                ],
                [
                    'title' => 'PCRE Extension',
                    'check' => function() {
                        return extension_loaded("pcre");
                    }
                ],
                [
                    'title' => 'SPL extension',
                    'check' => function() {
                        return extension_loaded("SPL");
                    }
                ],
                [
                    'title' => 'DOM extension',
                    'check' => function() {
                        return class_exists("DOMDocument", false);
                    }
                ],
                [
                    'title' => 'PDO extension',
                    'check' => function() {
                        return extension_loaded("pdo");
                    }
                ],
                [
                    'title' => 'PDO MySQL extension',
                    'check' => function() {
                        return extension_loaded("pdo_mysql");
                    }
                ],
                [
                    'title' => 'Mcrypt extension',
                    'check' => function() {
                        return extension_loaded("mcrypt");
                    }
                ],
                [
                    'title' => 'GD extension with FreeType support<br />or ImageMagick extension with <br/> PNG support',
                    'check' => function() {
                        if (extension_loaded('imagick')) {
                            $imagick = new Imagick();
                            $imagickFormats = $imagick->queryFormats('PNG');
                        }
                        if (extension_loaded('gd'))
                            $gdInfo = gd_info();
                        if (isset($imagickFormats) && in_array('PNG', $imagickFormats))
                            return true;
                        elseif (isset($gdInfo)) {
                            if ($gdInfo['FreeType Support'])
                                return true;
                            return false;
                        }
                        return false;
                    }
                ],
                [
                    'title' => 'Ctype extension',
                    'check' => function() {
                        return extension_loaded("ctype");
                    }
                ]
            ],
        ];

        if ($checkGroup == "") {
            return $checkLists;
        } else {
            return [$checkGroup => $checkLists[$checkGroup]];
        }
    }

    public static function checkInstall($checkGroup = "") {
        $checkList = Installer::getCheckList($checkGroup);
        $success = false;
        foreach ($checkList as $group => $groupItem) {
            foreach ($groupItem as $i => $c) {
                $check = $c['check']();
                if ($check !== true) {
                    Installer::setError($group, $i, $check);
                    $success = false;
                }
            }
        }

        return $success;
    }

    private static function fullPath() {
        $s = &$_SERVER;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        $uri = $protocol . '://' . $host . $s['REQUEST_URI'];
        $segments = explode('?', $uri, 2);
        $url = $segments[0];
        return $url;
    }

    public static function redirError($msg) {
        header("Location: " . Installer::fullPath() . "?r=install/index&msg=" . $msg);
    }

    public static function createIndexFile() {
        $path = Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "index.php";
        $file = file_get_contents($path);
        $file = str_replace(['$mode = "init"'], ['$mode = "install"'], $file);
        if (!is_file($path)) {
            return file_put_contents(Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php", $file);
        } else {
            $oldpath = Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php";
            $oldfile = file_get_contents($oldpath);
            if ($oldfile != $file) {
                return file_put_contents($oldpath, $file);
            } else {
                return true;
            }
        }
    }

    public static function init($config) {
        $config['defaultController'] = "install";
        $config['components']['db'] = [];

        $config['runtimePath'] = Setting::getRuntimePath();
        $config['components']['assetManager']['basePath'] = Setting::getAssetPath();

        if (Setting::$mode == "init") {
            if (!Installer::createIndexFile()) {
                if (!isset($_GET['msg'])) {
                    Installer::redirError(Yii::t("plansys", "Failed to write in '" . Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php" . "'"));
                    return $config;
                }
            } else {
                $url = explode("/plansys", Installer::fullPath());
                header("Location: " . $url[0] . "/index.php?r=install/index");
            }
        }

        return $config;
    }

}
