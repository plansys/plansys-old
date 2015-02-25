<?php

class Installer {

    private static $_errorList = [];

    public static function getErrorList() {
        return Installer::$_errorList;
    }

    public static function setError($group, $idx, $error) {
        if (!isset(Installer::$_errorList[$group])) {
            Installer::$_errorList[$group] = [];
        }
        Installer::$_errorList[$group][$idx] = $error;
    }

    public static function getError($group = "", $idx = -1) {
        if ($group == "") {
            return Installer::$_errorList;
        } else if (!isset(Installer::$_errorList[$group])) {
            return false;
        } else {
            if ($idx == -1) {
                return true;
            } else if (isset(Installer::$_errorList[$group][$idx])) {
                return Installer::$_errorList[$group][$idx];
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
        if (!empty($missing)) {
            return Setting::t('$_SERVER does not have {vars}.', array('{vars}' => implode(', ', $missing)));
        }

        if (realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(Setting::$entryScript)) {
            return Setting::t('$_SERVER["SCRIPT_FILENAME"] <br/> `{s}`<br/><br/> must be the same as the entry script file path `{r}`.', [
                        '{s}' => realpath($_SERVER["SCRIPT_FILENAME"]),
                        '{r}' => realpath(__FILE__)
            ]);
        }

        if (!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"])) {
            return Setting::t('Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');
        }

        if (!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0) {
            return Setting::t('Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');
        }

        return true;
    }

    public static function getCheckList($checkGroup = "") {
        $checkLists = [
            "Checking Directory Permission" => [
                [
                    "title" => 'Checking base directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getBasePath(), true);
                    }
                ],
                [
                    "title" => 'Checking app directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getAppPath());
                    }
                ],
                [
                    "title" => 'Checking assets directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getAssetPath(), true);
                    }
                ],
                [
                    "title" => 'Checking runtime directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getRuntimePath(), true);
                    }
                ],
                [
                    "title" => 'Checking repository directory permissions',
                    "check" => function() {
                        $repo = Setting::get('repo.path');
                        if (!is_dir($repo)) {
                            @mkdir($repo, 0777, true);
                        }

                        return Setting::checkPath(realpath($repo), true);
                    }
                ]
            ],
            "Checking Framework Requirements" => [
                [
                    'title' => 'Checking PHP Version ( > 5.5.0 )',
                    'check' => function() {
                        $result = version_compare(PHP_VERSION, "5.5.0", ">");
                        $msg = "Current PHP version is:" . PHP_VERSION;
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Reflection Extension',
                    'check' => function() {
                        $result = class_exists('Reflection', false);
                        $msg = "Reflection class does not exists!";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PCRE Extension',
                    'check' => function() {
                        $result = extension_loaded("pcre");
                        $msg = "Extension \"pcre\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'SPL extension',
                    'check' => function() {
                        $result = extension_loaded("SPL");
                        $msg = "Extension \"SPL\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'DOM extension',
                    'check' => function() {
                        $result = class_exists("DOMDocument", false);

                        $msg = "DomDocument class does not exists!";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PDO extension',
                    'check' => function() {
                        $result = extension_loaded("pdo");
                        $msg = "Extension \"pdo\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PDO MySQL extension',
                    'check' => function() {
                        $result = extension_loaded("pdo_mysql");
                        $msg = "Extension \"pdo_mysql\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Mcrypt extension',
                    'check' => function() {
                        $result = extension_loaded("mcrypt");
                        $msg = "Extension \"mcrypt\" is not loaded";
                        return $result !== true ? $msg : true;
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

                            return "GD Extension is loaded but no Freetype support";
                        }
                        return "GD Extension / ImageMagick is not loaded";
                    }
                ],
                [
                    'title' => 'Ctype extension',
                    'check' => function() {
                        $result = extension_loaded("ctype");
                        $msg = "Extension \"ctype\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Checking Server variables',
                    'check' => function() {
                        return Installer::checkServerVar();
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

    public static function createIndexFile() {
        $path = Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "index.php";
        $file = file_get_contents($path);
        $file = str_replace(['$mode = "init"'], ['$mode = "install"'], $file);
        if (!is_file($path)) {
            return @file_put_contents(Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php", $file);
        } else {
            $oldpath = Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php";
            $oldfile = @file_get_contents($oldpath);
            if ($oldfile != $file) {
                return @file_put_contents($oldpath, $file);
            } else {
                return true;
            }
        }
    }

    public static function init($config) {
        ## we hare to make sure the error page is shown
        ## so we need to strip yii unneeded config to make sure it is running

        $config['defaultController'] = "install";
        $config['components']['db'] = [];
        $config['components']['errorHandler'] = ['errorAction' => 'install/default/index'];

        Installer::checkInstall();

        if (Setting::$mode == "init") {
            if (!Installer::createIndexFile()) {
                Setting::redirError("Failed to write in \"{path}\" <br/> Permission denied", [
                    '{path}' => Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php"
                ]);
                return $config;
            } else {
                $url = explode("/plansys", Setting::fullPath());
                header("Location: " . $url[0] . "/index.php?r=install/default/index");
                die();
            }
        }

        return $config;
    }

}
