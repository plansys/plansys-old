<?php


class WebSocketController extends CComponent {
     private $wsserver;
     
     function __construct($wsserver) {
          $this->wsserver = $wsserver;
     }
     
     public function broadcast($msg, $to = []) {
          if (!is_string($msg)) {
               $msg = json_encode($msg);
          }
          
          $this->wsserver->send($msg, $to);
     }
     
     public function set($key, $value, $client = []) {
          $this->wsserver->set($key, json_encode($value), $client);
     }
     
     public function get($key, $client = [], $decode = true) {
          if ($decode) {
               return json_decode($this->wsserver->get($key, $client), true);
          } else {
               return $this->wsserver->get($key, $client);
          }
     }
     
     public function getClients($client = []) {
          return $this->wsserver->getClients($client);
     }
     
     public function getWsname() { // masih salah
          $ref = new ReflectionClass($this); 
          return $ref->getFileName();
     }
     
     public function setTag($client, $tag) {
          $this->wsserver->setTag($client, $tag);
     }
     
     /* these function will be overidden */
     public function connected($client) {}
     public function disconnected ($client, $reason) {}
     public function received($msg, $from) {}
}