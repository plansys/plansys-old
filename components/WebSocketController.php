<?php


class WebSocketController {
     private $wsserver;
     
     private static function getWsName() {
          $class = get_called_class();
          $ref = new ReflectionClass($class);
          $classPath = $ref->getFileName();
          if (strpos($classPath, Setting::getRootPath()) === 0) {
               $path = trim(substr($classPath, strlen(Setting::getRootPath())), DIRECTORY_SEPARATOR);
               $path = explode(DIRECTORY_SEPARATOR, $path);
               
               if (count($path) > 3 && $path[1] == 'modules') {
                    return $path[2] . '/' . substr(lcfirst($class), 0, -2);
               } else {
                    return $class;
               }
          } else {
               return $class;
          }
     }
     
     public static function __callStatic($name, $params) {
          $class = get_called_class();
          if (method_exists($class, '_' . $name)) {
               $wsserver = new WebSocketServer([
                    'tid' => self::getWsName(),
                    'uid' => '_',
                    'sid' => '_',
                    'cid' => '_'
               ]);
               $ws = new $class($wsserver);
               return call_user_func_array([$ws, '_' . $name], $params);
          } else{
               throw new Exception('Call to undefined method ' . get_class() . '::' . $name . '()');
          }
     }
     
     public function __call($name, $params) {
          
     } 
     
     function __construct($wsserver) {
          $this->wsserver = $wsserver;
     }
     
     private function _broadcast($msg, $to = []) {
          if (!is_string($msg)) {
               $msg = json_encode($msg);
          }
          
          $this->wsserver->send($msg, $to);
     }
     
     private function _set($key, $value, $client = []) {
          $this->wsserver->set($key, json_encode($value), $client);
     }
     
     private function get($key, $client = [], $decode = true) {
          if ($decode) {
               return json_decode($this->wsserver->get($key, $client), true);
          } else {
               return $this->wsserver->get($key, $client);
          }
     }
     
     private function _getClients($client = []) {
          return $this->wsserver->getClients($client);
     }
     
     private function _getWsname() { // masih salah
          return self::getWsName();
     }
     
     private function _setTag($client, $tag) {
          $this->wsserver->setTag($client, $tag);
     }
     
     /* these function will be overidden */
     public function connected($client) {}
     public function disconnected ($client, $reason) {}
     public function received($msg, $from) {}
}