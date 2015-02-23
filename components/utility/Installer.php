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
