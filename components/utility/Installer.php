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
                        return extension_loaded("DOMDocument");
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
                    'title' => 'SOAP extension',
                    'check' => function() {
                        return extension_loaded("soap");
                    }
                ],
                [
                    'title' => 'GD extension with<br />FreeType support<br />or ImageMagick<br />extension with<br />PNG support',
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
                ],
                [
                    'title' => 'Fileinfo extension',
                    'check' => function() {
                        return extension_loaded("fileinfo");
                    }
                ]
            ],
            "Checking Database" => [
                [
                    "title" => "Checking Database Connection",
                    "check" => function() {
                        return true;
                    }
                ]
            ]
        ];

        if ($checkGroup == "") {
            return $checkLists;
        } else {
            return [$checkGroup => $checkLists[$checkGroup]];
        }
    }

    public static function checkInstall($checkGroup = "") {
        $checkList = Installer::getCheckList($checkGroup);

        foreach ($checkList as $group => $groupItem) {
            foreach ($groupItem as $i => $c) {
                $check = $c['check']();
                if ($check !== true) {
                    Installer::setError($group, $i, $check);
                    return false;
                }
            }
        }

        return true;
    }

    public static function init($config) {
        $config['defaultController'] = "install";
        $config['components']['db'] = [];

        return $config;
    }

}
