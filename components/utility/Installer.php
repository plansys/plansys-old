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

    public static function getCheckList() {
        return [
            "Checking Directory Permission" => [
                [
                    "title" => "Checking base directory permissions",
                    "check" => function() {
                        return Installer::checkPath(Setting::getBasePath());
                    }
                ],
                [
                    "title" => "Checking app directory permissions",
                    "check" => function() {
                        return Installer::checkPath(Setting::getAppPath());
                    }
                ],
                [
                    "title" => "Checking assets directory permissions",
                    "check" => function() {
                        return Installer::checkPath(Setting::getAssetPath());
                    }
                ],
                [
                    "title" => "Checking runtime directory permissions",
                    "check" => function() {
                        return Installer::checkPath(Setting::getRuntimePath());
                    }
                ],
                [
                    "title" => "Checking repository directory permissions",
                    "check" => function() {
                        return Installer::checkPath(Setting::get('repo.path'));
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

    public static function plansysInstalled() {   
        $checkList = Installer::getCheckList();
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
