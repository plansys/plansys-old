<?php
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TMultiplexedProtocol;

class ServiceManager extends CComponent {
     public $socket, $transport, $protocol, $client;
     private static $sm;
     
     private static function _open() {
          if (!class_exists('\svc\ServiceManagerClient')) {
               include(Yii::getPathOfAlias('application.components.thrift.client.svc.Types') . ".php");
               include(Yii::getPathOfAlias('application.components.thrift.client.svc.ServiceManager') . ".php");
          }
          
          $portfile = @file_get_contents(Yii::getPathOfAlias('webroot.assets.ports') . ".txt");
          if (is_null($portfile)) {
              throw new Exception('Thrift Daemon is not running!'); 
          }
          $port = explode(":", $portfile);
          
          self::$sm = new ServiceManager;
          try {
               self::$sm->socket = new TSocket('127.0.0.1', $port[0]);
               self::$sm->transport = new TBufferedTransport(self::$sm->socket, 1024, 1024);
               self::$sm->protocol = new TMultiplexedProtocol(new TCompactProtocol(self::$sm->transport), 'ServiceManager');
               self::$sm->client = new \svc\ServiceManagerClient(self::$sm->protocol);
               
               self::$sm->transport->open();
          } catch (TException $tx) {
               print 'TException: '.$tx->getMessage()."\n";
          }
     }
     
     private static function _close() {
          try {
               self::$sm->transport->close();
          } catch (TException $tx) {
               print 'TException: '.$tx->getMessage()."\n";
          }
     }
     
     public static function getAllServices() {
          self::_open();
          $services = self::$sm->client->getAllServices();
          $plansys = [];
          $regular = [];
          foreach ($services as $svc) {
               $schedule = $svc->schedule;
               if ($schedule != 'manual') {
                    if ($svc->period > 1) {
                         $schedule = "Every {$svc->period} {$schedule}s";
                    } else {
                         $schedule = "Every {$schedule}";
                    }
               } else {
                    $schedule = "Manual";
               }
               
               $isPlansys = strpos($svc->commandPath, 'application.') === 0;
               if (@$svc->status != 'ok') {
                    $status = 'draft'; 
               } else {
                    $status = empty($svc->runningInstances) ? "stopped" : "running";
               }
               
               $res = [
                    'is_plansys' => $isPlansys, 
                    'name' => $svc->name,
                    'command' => $svc->command,
                    'action' => $svc->action,
                    'schedule' => $schedule,
                    'status' => $status,
                    'running_instances' => count($svc->runningInstances),
                    'lastRun' => Helper::timeAgo($svc->lastRun)
               ];
               
               if ($isPlansys) {
                    $plansys[] = $res;
               } else {
                    $regular[] = $res;
               }
          }
          self::_close();
          usort($plansys, function ($a, $b) {
              return strcmp($a['name'], $b['name']);
          });
          usort($regular, function ($a, $b) {
              return strcmp($a['name'], $b['name']);
          });
          return array_merge($plansys, $regular);
     }
     
     public static function getService($name) {
          self::_open();
          try {
               $svc = self::$sm->client->getService($name);
               $svc->runningInstances = is_null($svc->runningInstances) ? [] : array_values($svc->runningInstances);
               $svc->stoppedInstances = is_null($svc->stoppedInstances) ? [] : array_values($svc->stoppedInstances);
          } catch (Exception $e) {
               self::_close();
               return null;
          }
          self::_close();
          return $svc;
     }
     
     public static function getRunningInstance($name) {
          self::_open();
          $services = self::$sm->client->getService($name);
          $instances = $services->runningInstances;
          $results = [];
          if (isset($instances)) {
               foreach ($instances as $itc) {
                    $svc = self::$sm->client->getInstance($itc->pid);
                    if (!is_null($svc)) {
                         $results[] = $svc;
                    }
               }
          }
          self::_close();
          return $results;
     }
     
     public static function start($name, $params = null) {
          self::_open();
          $pid = self::$sm->client->start($name, json_encode($params));
          self::_close();
          return $pid;
     }
     
     public static function run($name, $params = null) {
          self::start($name, $params);
     }
     
     public static function stop($name) {
          self::_open();
          self::$sm->client->stop($name);
          self::_close();
     }
     
     public static function add($service) {
          self::_open();
          self::$sm->client->add(new \svc\Service ($service));
          self::_close();
     }
     
     public static function remove($serviceName) {
          self::_open();
          self::$sm->client->remove($serviceName);
          self::_close();
     }
     
     public static function setView($serviceName, $key, $value) {
          self::_open();
          self::$sm->client->setView($serviceName, $key, $value);
          self::_close();
     }
     
     public static function getFilePath($service) {
          $path = Yii::getPathOfAlias($service->commandPath . "/" . $service->command)  . ".php";
          if (is_file($path)) return $path;
          else return false;
     }
     
     public static function importJson() {
          $jsonpath = Yii::getPathOfAlias("app.config.service") . ".json";
          if (is_file($jsonpath)) {
               $json = json_decode(@file_get_contents($jsonpath), true);
               if (!is_null($json['list'])) {
                    foreach ($json['list'] as $sname => $svc) {
                         echo $sname;
                         if ($sname != 'SendEmail' && $sname != 'ImportData') {
                              self::add([
                                   'name' => @$svc['name'],
                                   'commandPath' => @$svc['commandPath'],
                                   'command' => @$svc['command'],
                                   'action' => @$svc['action'],
                                   'schedule' => @$svc['schedule'],
                                   'period' => @$svc['period'],
                                   'instanceMode' => @$svc['instance'],
                                   'singleInstanceAction' => @$svc['singleInstanceMode'],
                                   'status' => @$svc['status'],
                                   'lastRun' => @$svc['lastRun']
                              ]);
                         }
                    }
               }
          }
     }
     
     public static function startDaemon() {
          try {
               self::_open();
               self::_close();
          } catch(Exception $e) { 
               # thrift server is not running, then we should run it
               $path = Yii::getPathOfAlias("application.components.thrift.server");
               $file = $path;
               
               if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    $file = $file . DIRECTORY_SEPARATOR . "server.exe";
               } else {
                    $file = $file . DIRECTORY_SEPARATOR . "server";
               }
               
               if (!Setting::get('app.phpPath')) {
                    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                         exec("type php", $output);
                         if (count($output) > 0) {
                              $output = explode(" is ", $output[0]);
                              if (count($output) > 0) {
                                   Setting::set('app.phpPath', $output[1]);
                              }
                         }
                    }
               }

               $svpath = Yii::getPathOfAlias("app.config.service") . ".buntdb";
               $svtpath = Yii::getPathOfAlias("assets.service") . ".buntdb";
               $isnew = false;
               if (!is_file($svpath) && !is_file($svtpath)) {
                    $isnew = true; 
               }
               
               $output = shell_exec($file);
               sleep(1);
               
               if ($isnew) {
                    self::importJson();
               }
               
          }
          
     }
}
