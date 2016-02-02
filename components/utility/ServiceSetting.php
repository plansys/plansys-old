<?php

class ServiceSetting {
    public static $default = [ 
        "services"=> [
            "list"=> [
                "SendEmail" => [
                    "name"=> "SendEmail",
                    "commandPath"=> "application.commands",
                    "command"=> "EmailCommand",
                    "action"=> "actionSend",
                    "schedule"=> "manual",
                    "period"=> "",
                    "instance"=> "single",
                    "singleInstanceMode"=> "wait"
                ],
                "ImportData"=> [
                    "name"=> "ImportData",
                    "commandPath"=> "application.commands",
                    "command"=> "ImportCommand",
                    "action"=> "actionIndex",
                    "schedule"=> "manual",
                    "period"=> "",
                    "instance"=> "parallel",
                    "singleInstanceMode"=> "wait",
                ]
            ],
            "daemon"=> [
                "isRunning"=> false
            ]
        ]
    ];
    public static $data = [];
    private static $path = "";
    
    private static function getPath() {
        if (self::$path == "") {
            $ap            = Setting::$rootPath . DIRECTORY_SEPARATOR . "app";
            self::$path = $ap . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "service.json";
        }
        
        return self::$path;
    }
    
    public static function get($key, $default = null, $forceRead = false) {
        $keys = explode('.', $key);

        if ($forceRead) {
            $file = @file_get_contents(self::getPath());   
            $setting       = json_decode($file, true);
            Setting::$data = self::arrayMergeRecursiveReplace(self::$default, $setting);
        }

        $arr = Setting::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        if ($arr == null) {
            $arr = $default;
        }

        return $arr;
    }
    
    public static function set($key, $value, $flushSetting = true) {
        Setting::setInternal(Setting::$data, $key, $value);

        if ($flushSetting) {
            Setting::write();
        }
    }

    private static function setInternal(& $arr, $path, $value) {
        $keys = explode('.', $path);

        while ($key = array_shift($keys)) {
            $arr = &$arr[$key];
        }

        $arr = $value;
    }
    
    private static function arrayMergeRecursiveReplace($paArray1, $paArray2) {
        if (!is_array($paArray1) or !is_array($paArray2)) {
            return $paArray2;
        }
        foreach ($paArray2 AS $sKey2 => $sValue2) {
            $paArray1[$sKey2] = Setting::arrayMergeRecursiveReplace(@$paArray1[$sKey2], $sValue2);
        }
        return $paArray1;
    }
    
}