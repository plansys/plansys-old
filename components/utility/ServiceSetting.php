<?php

class ServiceSetting {
    public static $default = [ 
        "list"=> [
            "SendEmail" => [
                "name"=> "SendEmail",
                "commandPath"=> "application.commands",
                "command"=> "EmailCommand",
                "action"=> "actionSend",
                "schedule"=> "manual",
                "period"=> "",
                "instance"=> "single",
                "singleInstanceMode"=> "wait",
                "status" => "ok"
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
                "status" => "ok"
            ]
        ]
    ];
    public static $data = [];
    private static $path = "";
    private static $isRead = false;
    
    private static function getPath() {
        if (self::$path == "") {
            $ap            = Setting::$rootPath . DIRECTORY_SEPARATOR . "app";
            self::$path = $ap . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "service.json";
        }
        
        return self::$path;
    }
    
    public static function remove($key, $flushSetting = true) {
        $keys = explode('.', $key);

        if (empty(self::$data)) {
            $file = @file_get_contents(self::getPath());   
            $setting       = json_decode($file, true);
            self::$data = self::arrayMergeRecursiveReplace(self::$default, $setting);
        }

        $arr = &self::$data;
        while ($k = array_shift($keys)) {
            $arr    = &$arr[$k];
            $length = count($keys);

            if ($length == 1) {
                unset($arr[$keys[0]]);
                break;
            }
        }

        if ($flushSetting) {
            self::write();
        }
    }
    
    public static function get($key, $default = null, $forceRead = false) {
        $keys = explode('.', $key);
        
        if ($forceRead || !self::$isRead || empty(self::$data)) {
            $file = @file_get_contents(self::getPath());   
            if (!$file) {
                self::$data = self::$default;
                self::write();
            } else {
                $setting       = json_decode($file, true);
                self::$data = self::arrayMergeRecursiveReplace(self::$default, $setting);
            }
            self::$isRead = true;
        }


        $arr = self::$data;
        while ($k = array_shift($keys)) {
            $arr = &$arr[$k];
        }

        if ($arr == null) {
            $arr = $default;
        }
        
        $keys = explode('.', $key);
        if (substr($key,0,4) == "list") {
            if (count($keys) == 1) {
                foreach ($arr as $k=>$a) {
                    $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $k);
                    if (is_file($path . '/lastrun.txt')) {
                        $arr[$k]['lastRun'] = file_get_contents($path . '/lastrun.txt'); 
                    }
                }
            } else if (count($keys) == 2) {
                $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $keys[1]);
                if (is_file($path . '/lastrun.txt')) {
                    $arr['lastRun'] = file_get_contents($path . '/lastrun.txt'); 
                }
            }
        }

        return $arr;
    }
    
    public static function set($key, $value, $flushSetting = true) {
        if (empty(self::$data)) {
            $file = @file_get_contents(self::getPath());
            self::$data = json_decode($file, true);
        }
        
        self::setInternal(self::$data, $key, $value);

        if (Helper::isLastString($key, ".lastRun")) {
            $keys = explode(".", $key);
            $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $keys[1]);
            
            if (!is_dir($path)) {
                mkdir($path, 075, true);
                chmod($path, 0755);
            }
            file_put_contents($path . '/lastrun.txt', $value);
        } else if ($flushSetting) {
            self::write();
        }
    }

    public static function write() {
        $result = @file_put_contents(self::getPath(), json_encode(self::$data, JSON_PRETTY_PRINT));
    }

    private static function setInternal(&$arr, $path, $value) {
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
            $paArray1[$sKey2] = self::arrayMergeRecursiveReplace(@$paArray1[$sKey2], $sValue2);
        }
        return $paArray1;
    }
    
}