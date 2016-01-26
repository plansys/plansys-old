<?php

class ServiceManager extends CComponent {
    const MAX_LOG_COUNT = 10;
    
    public static function getAllServices() {
        $services = Setting::get('services.list');
        $results = [];
        foreach ($services as $svc) {
            $instances = ServiceManager::getRunningInstance($svc['name']);
            $schedule = $svc['schedule'];
            if ($schedule != 'manual') {
                if ($svc['period'] > 1) {
                    $schedule = "Every {$svc['period']} {$schedule}s";
                } else {
                    $schedule = "Every {$schedule}";
                }
            } else {
                $schedule = "Manual";
            }
            
            $res = [
                'is_plansys' => strpos($svc['commandPath'], 'application.') === 0, 
                'name' => $svc['name'],
                'command' => $svc['command'],
                'action' => $svc['action'],
                'schedule' => $schedule,
                'status' => empty($instances) ? "stopped" : "running",
                'running_instances' => count($instances),
                'last_run' => $svc['lastRun']
            ];
            $results[] = $res;
        }
        return $results;
    }
    
    public static function getRunningInstance($name) {
        $path = Yii::getPathOfAlias('root.assets.services.running.' . $name);
        $initPath = Yii::getPathOfAlias('root.assets.services.init');
        if (is_dir($path)) {
            $instances = glob($path . DIRECTORY_SEPARATOR . "*");
            $results = [];
            foreach ($instances as $itc) {
                $id = str_replace($path, "", $itc);
                $svc = ServiceManager::getInstance($id);
                if (!is_null($svc)) {
                    $results[] = $svc;
                }
            }
            return $results;
        } 
        return [];
    }
    
    public static function getInstanceByName($name) {
        $path = Yii::getPathOfAlias('root.assets.services.running.' . $name);
        $initPath = Yii::getPathOfAlias('root.assets.services.init');
        if (is_dir($path)) {
            $instances = glob($path . DIRECTORY_SEPARATOR . "*");
            foreach ($instances as $itc) {
                $id = str_replace($path, "", $itc);
                $svc = ServiceManager::getInstance($id);
                if (!is_null($svc)) {
                    return $svc;
                }
            }
        } 
        return null;
    }
    
    public static function getInstance($id) {
        $init = ServiceManager::getInitLogPath($id);
        if (is_file($init)) {
            $svc = json_decode(file_get_contents($init), true);
            if (!is_null($svc)) {
                return $svc;
            }
        }
        
        return null;
    }
    
    public static function isDaemonStarted() {
        $daemon = Setting::get('services.daemon', [
            'isRunning' => false,
            'pid' => 0
        ]); 
        if ($daemon['isRunning'] && $daemon['pid'] != 0) {
            if (!ServiceManager::checkPid($daemon['pid'])) {
                Setting::set('services.daemon.isRunning', false);
                return false;
            } else {
                return true;
            }
        }
        return false;
    }

    public static function startDaemon() {
        $daemon = Setting::get('services.daemon', [
            'isRunning' => false,
            'pid' => 0
        ]); 
        if (!$daemon['isRunning']) {
            $pid = ServiceManager::process("run php yiic.php service startDaemon");
            if(!empty($pid)){
                Setting::set('services.daemon', [
                    'isRunning' => true,
                    'pid' => $pid[0]
                ]);
            }
        } else {
            if (!ServiceManager::checkPid($daemon['pid'])) {
                $pid = ServiceManager::process("run php yiic.php service startDaemon");
                if(!empty($pid)){
                    Setting::set('services.daemon', [
                        'isRunning' => true,
                        'pid' => $pid[0]
                    ]);
                    
                }
            }
        }
    }
    
    public static function stopDaemon() {
        $daemon = Setting::get('services.daemon', [
            'isRunning' => false,
            'pid' => 0
        ]);
        
        if (!!$daemon['isRunning'] && $daemon['pid'] != 0) {
            if (self::isProcessRunning($daemon['pid'])) {
                $output = self::process("kill {$daemon['pid']}");
            }
            
            Setting::set('services.daemon', [
                'isRunning' => false,
                'pid' => 0
            ]);
        }
    }
    
    public static function process($command) {
        chdir(Yii::getPathOfAlias('application'));
        $process = self::getProcessCommand();
        exec("{$process} {$command}", $results);
        return $results;
    }
    
    public static function start($serviceName, $params = null) {
        $pid = ServiceManager::run($serviceName, $params);
        $controller = Yii::app()->controller;
        if (is_null($pid)) {
            $msg = "Service {$serviceName} not found";
            
            if (!is_null($controller)) {
                $controller->renderForm("SysServiceNotFound", null, [
                    'msg' => $msg
                ]);
            } else {
                echo $msg;
            }
        } else {
            $full = 0;
            if (isset($_GET['full'])) {
                $full = $_GET['full'];
            }
            
            if (!is_null($controller)) {
                $controller->redirect(['/sys/service/view', 'name' => $serviceName, 'id' => $pid, 'full' => $full]);
            } else {
                echo "OK";
            }
        }
    }

    public static function run($serviceName, $params = null) {
        $service = Setting::get('services.list.' . $serviceName);
                
        if ($service) {
            if (!is_null($params)) {
                $service['params'] = $params;
            }
            
            return ServiceManager::runInternal($serviceName, $service);
        }
    }

    public static function checkPid($pid) {
        $command = "find {$pid}";
        $ret = ServiceManager::process($command);
        if (@$ret[0] === "false") {
            return false;
        }
        return true;
    }
    
    public static function runInternal($serviceName, $service = null) {
        if (is_null($service)) {
            $service = Setting::get('services.list.' . $serviceName);
        }
        
        if ($service) {
            if ($service['instance'] == 'single') {
                $running = ServiceManager::getRunningInstance($service['name']);
                if (count($running) > 0) {
                    if ($service['singleInstanceMode'] == 'wait') {
                        return false;
                    } else {
                        ServiceManager::kill($service['name']);
                    }
                }
            }
            
            $id = ServiceManager::initInstance();
            $logPath = ServiceManager::getLogPath($serviceName, $id);
            $command = "runLog \"{$logPath}\" php yiic.php service execute --id={$id}";
            $pid = ServiceManager::process($command);
            
            if (!empty($pid)) {
                $service['pid'] = $pid[0];
                ServiceManager::sendMsg($id, $service);
                
                return $id;
            } else {
                return false;
            }
        } 
    }
    
    
    public static function kill($name) {
        $result = ServiceManager::getRunningInstance($name);
        foreach ($result as $r) {
            ServiceManager::log($name, $r['id'], "SERVICE: " . str_pad("Killing instance #{$r['id']}" , 45, "."));
            ServiceManager::process("kill {$r['pid']}");
            ServiceManager::logAppend($name, $r['id'], "[OK]");
            ServiceManager::markAsStopped($name, $r['id']);
        }
    }
    
    public static function getInfo($id) {
        $init = ServiceManager::getInitLogPath($id);
        if (is_file($init)) {
            return json_decode(file_get_contents($init));
        } else {
            return false;
        }
    }
    
    public static function sendMsg($id, $msg) {
        $path = Yii::getPathOfAlias('root.assets.services.msg');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        if (is_array($msg)) {
            file_put_contents($file . ".json", json_encode($msg));
        } else if (is_string($msg)) {
            file_put_contents($file . ".txt", json_encode($msg));
        }
    }
    
    public static function hasMsg($id) {
        $path = Yii::getPathOfAlias('root.assets.services.msg');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        
        if (is_file($file . ".json")) return true;
        if (is_file($file . ".txt")) return true;
        return false;
    }
    
    public static function readMsg($id) {
        $path = Yii::getPathOfAlias('root.assets.services.msg');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        $fileExist = false;
        if (is_file($file . ".json")) {
            $file = $file . ".json";
            $fileExist = "json";
        }
        if (is_file($file . ".txt")) {
            $file = $file . ".txt";
            $fileExist = "txt";
        }
        
        if ($fileExist) {
            $content = file_get_contents($file);
            if ($fileExist == "json")  {
                $content = json_decode($content, true);
            }
            unlink($file);
            return $content;
        }
        return false;
    }
    
    public static function initInstance() {
        $id = uniqid();
        $path = Yii::getPathOfAlias('root.assets.services.init');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        while (is_file($file)) {
            $id = uniqid();
            $file = $path . DIRECTORY_SEPARATOR . $id;
        }
        ServiceManager::initLog($id, str_pad("Starting instance #{$id}", 45, "."));
        return $id;
    }
    
    public static function markAsRunning($serviceName, $id, $service) {
        $path = Yii::getPathOfAlias('root.assets.services.init');
        $old = $path . DIRECTORY_SEPARATOR . $id;
        $new = ServiceManager::getLogPath($serviceName, $id);
        
        if (is_file($old)) {
            copy($old, $new);
            Setting::set('services.list.' . $serviceName . '.lastRun', date("Y-m-d H:i:s"));
            file_put_contents($old, json_encode($service, JSON_PRETTY_PRINT));
        }
    }
    
    public static function getStoppedInstance($serviceName, $id) {
        $initPath = self::getStoppedInitPath($serviceName, $id);
        $logPath = self::getStoppedInitPath($serviceName, $id);
        
        if ($initPath != "") {
            $init = file_get_contents($initPath);
            return json_decode($init, true);
        }
        
        return false;
    }
    
    private static function saveStoppedLog($serviceName, $id, $path, $old) {
        ## only retain maximum X stopped logs 
        $logs = glob($path . DIRECTORY_SEPARATOR . "*");
        natsort($logs);
        $logs = array_values($logs);
        $count = count($logs);
        if ($count > 0) {
            $count = explode(".", str_replace($path  . DIRECTORY_SEPARATOR , "", $logs[$count -1]))[0]; 
        }
        $new = $path . DIRECTORY_SEPARATOR . ($count + 1) . "." . $id;
        rename($old, $new);
        if ($count > ServiceManager::MAX_LOG_COUNT) {
            unlink($logs[0]);
        }
    }
    
    public static function markAsStopped($serviceName, $id) {
        $logPath = Yii::getPathOfAlias('root.assets.services.stopped.' . $serviceName . ".log");
        $initPath = Yii::getPathOfAlias('root.assets.services.stopped.' . $serviceName . ".init");
        if (!is_dir($logPath)) {
            mkdir($logPath, 0777, true);
        }
        
        if (!is_dir($initPath)) {
            mkdir($initPath, 0777, true);
        }
        $oldLog  = ServiceManager::getLogPath($serviceName, $id);
        $oldInit = Yii::getPathOfAlias('root.assets.services.init') .DIRECTORY_SEPARATOR . $id;
        
        if (is_file($oldLog) && is_file($oldInit)) {
            self::saveStoppedLog($serviceName, $id, $initPath, $oldInit);
            self::saveStoppedLog($serviceName, $id, $logPath, $oldLog);
        }
    }
    
    public static function initLog($id, $msg) {
        $path = Yii::getPathOfAlias('root.assets.services.init');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        $date = date("Y-m-d H:i:s");
        file_put_contents($file, "\n[{$date}] SERVICE: {$msg}", FILE_APPEND);
    }
    
    public static function initLogAppend($id, $msg) {
        $path = Yii::getPathOfAlias('root.assets.services.init');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        file_put_contents($file, "{$msg}", FILE_APPEND);
    }
    
    private static function getInitLogPath($id) {
        $path = Yii::getPathOfAlias('root.assets.services.init');
        $file = $path . DIRECTORY_SEPARATOR . $id;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return $file;
    }
    
    private static function getLogPath($serviceName, $id) {
        $path = Yii::getPathOfAlias('root.assets.services.running.' . $serviceName);
        $file = $path . DIRECTORY_SEPARATOR . $id;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return $file;
    }
    
    
    private static function getStoppedInitPath($serviceName, $id) {
        $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $serviceName . ".init");
        $files = glob($path . DIRECTORY_SEPARATOR . "*." . $id);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        if (!empty($files)) {
            return $files[0];
        }
        return "";
    }
    
    
    private static function getStoppedLogPath($serviceName, $id) {
        $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $serviceName . ".log");
        $files = glob($path . DIRECTORY_SEPARATOR . "*." . $id);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        if (!empty($files)) {
            return $files[0];
        }
        return "";
    }
    
    public static function readLog($serviceName, $id = null, $lines = 20) {
        if (is_null($id)) {
            $path = Yii::getPathOfAlias('root.assets.services.stopped.' . $serviceName . ".log");
            $files = glob($path . DIRECTORY_SEPARATOR . "*");
            if (!empty($files)) {
                natsort($files);
                return Helper::ReadFromEndByLine(array_pop($files), $lines);
            }
        }
        
        $file = self::getLogPath($serviceName, $id);
        if (is_file($file)) {
            return Helper::ReadFromEndByLine($file, $lines);
        } else {
            $file = self::getStoppedLogPath($serviceName, $id);
            if (is_file($file)) {
                return Helper::ReadFromEndByLine($file, $lines);
            } else {
                $file = self::getInitLogPath($id);
                if (is_file($file)) {
                    return Helper::ReadFromEndByLine($file, $lines);
                }
            }
        }
        
        return "";
    }
    
    public static function logAppend($serviceName, $id, $msg) {
        $file = self::getLogPath($serviceName, $id);
        if (is_file($file)) {
            file_put_contents($file, "{$msg}", FILE_APPEND);
        }
    }
    
    public static function log($serviceName, $id, $msg) {
        $file = self::getLogPath($serviceName, $id);
        $date = date("Y-m-d H:i:s");
        if (is_file($file)) {
            file_put_contents($file, "\n[{$date}] {$msg}", FILE_APPEND);
        }
    }
    
    private static function getProcessCommand() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'process.exe';
        } else if (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN'){
            return './process.osx';
        } else if (strtoupper(substr(PHP_OS, 0, 5)) === 'LINUX') {
            if (!getenv('PATH')) {
                putenv('PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/usr/games:/usr/local/games');
            }
            
            return './process.linux';
        } 
    }
    
    private static function isProcessRunning($pid) {
        $output = ServiceManager::process("find {$pid}");
        return ((strtolower(trim($output[0])) == 'false') ? false : true);
    }
    
}