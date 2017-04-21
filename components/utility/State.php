<?php
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TMultiplexedProtocol;

class State extends CComponent {
     public $socket, $transport, $protocol, $client;
     private static $sm;
     
     private static function _open() {
          if (!class_exists('\state\StateManagerClient')) {
               include(Yii::getPathOfAlias('application.components.thrift.client.state.Types') . ".php");
               include(Yii::getPathOfAlias('application.components.thrift.client.state.StateManager') . ".php");
          }
          
          $portfile = @file_get_contents(Yii::getPathOfAlias('webroot.assets.ports') . ".txt");
          if (is_null($portfile)) {
              throw new Exception('Thrift Daemon is not running!'); 
          }
          $port = explode(":", $portfile);
          
          self::$sm = new State;
          try {
               self::$sm->socket = new TSocket('127.0.0.1', $port[0]);
               self::$sm->transport = new TBufferedTransport(self::$sm->socket, 1024, 1024);
               self::$sm->protocol = new TMultiplexedProtocol(new TCompactProtocol(self::$sm->transport), 'StateManager');
               self::$sm->client = new \state\StateManagerClient(self::$sm->protocol);
               
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
     
     public static function set($key, $value) {
          self::_open();
          
          if (!is_string($value)) {
               $value = json_encode($value);
          }
          
          self::$sm->client->stateSet($key, $value);
          self::_close();
     }
     
     public static function get($key) {
          try {
               self::_open();
               $result = self::$sm->client->stateGet($key);
               self::_close();
               
               return $result;
          } catch(Exception $e) {
               return null;
          }
     }
     
     public static function del($key) {
          self::_open();
          $result = self::$sm->client->stateDel($key);
          self::_close();
          
          return $result;
     }
}