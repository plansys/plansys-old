<?php
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TMultiplexedProtocol;

class State extends CComponent {
     private $manualclose, $db, $socket, $transport, $protocol, $client, $indexes;
     
     public static function db($db, $manualclose = false) {
          return new State($db, $manualclose = false);
     }
     
     public function __construct($db, $manualclose = false) {
          $this->db = $db;
          $this->manualclose = $manualclose;
          if ($this->manualclose) {
               $this->open();
          }
          return $this;
     }
     
     public function open() {
          if (!class_exists('\state\StateManagerClient')) {
               include(Yii::getPathOfAlias('application.components.thrift.client.state.Types') . ".php");
               include(Yii::getPathOfAlias('application.components.thrift.client.state.StateManager') . ".php");
          }
          $portpath = Yii::getPathOfAlias('root.assets.ports') . ".txt";
          $portfile = file_get_contents($portpath);
          if (is_null($portfile)) {
              throw new Exception('Thrift Daemon is not running!'); 
          }
          $port = explode(":", $portfile);
          try {
               $this->socket = new TSocket('127.0.0.1', $port[0]);
               $this->socket->setSendTimeout(60000);
               $this->socket->setRecvTimeout(60000);
               $this->transport = new TBufferedTransport($this->socket, 1024, 1024);
               $this->protocol = new TMultiplexedProtocol(new TCompactProtocol($this->transport), 'StateManager');
               $this->client = new \state\StateManagerClient($this->protocol);
               
               $this->transport->open();
          } catch (TException $tx) {
               print 'TException: '.$tx->getMessage()."\n";
               die();
          }
     }
     
     public function close() {
          try {
               $this->transport->close();
          } catch (TException $tx) {
               print 'TException: '.$tx->getMessage()."\n";
          }
     }
     
     public function set($key, $value) {
          if (!is_string($value)) {
               $value = json_encode($value);
          }
          
          if (!$this->manualclose) $this->open();
          $this->client->stateSet($this->db, $key, $value);
          if (!$this->manualclose) $this->close();
     }
     
     public function get($key) {
          try {
               if (!$this->manualclose) $this->open();
               $result = $this->client->stateGet($this->db, $key);
               if (!$this->manualclose) $this->close();
          } catch (Exception $e) {
               $result = null;
          }  
          return $result;
     }
     
     public function getByKey($pattern = "*") {
          if (!$this->manualclose) $this->open();
          try {
               $result = $this->client->stateGetByKey($this->db, $pattern);
          } catch (Exception $e) {
               $result = [];
          }  
          
          $result = $this->formatResultAsArray($result);
          if (!$this->manualclose) $this->close();
          return $result;
     }
     
     private function formatResultAsArray($result) {
          foreach ($result as $k=>$v) {
               $result[$k]["val"] = json_decode($v["val"], true);
          }
          return $result;
     }
     
     public function count() {
          try {
               if (!$this->manualclose) $this->open();
               $result = $this->client->stateCount($this->db);
               if (!$this->manualclose) $this->close();
          } catch (Exception $e) {
               $result = null;
          }  
          return $result;
     }
     
     public function createIndex($name, $pattern = "*", $type="string") {
          if (!$this->manualclose) $this->open();
          $this->client->stateCreateIndex($this->db, $name, $pattern, $type);
          $this->indexes[$name] = $type;
          if (!$this->manualclose) $this->close();
     }
     
     public function getByIndex($name, $params = []) { 
          try {
               if (!$this->manualclose) $this->open();
               $params = array_merge([
                    'startfrom' => 'first',
                    'pattern' => '*',
                    'pivot' => '',
                    'itemperpage' => '',
                    'page' => ''
               ], $params);
               $params['startfrom'] = @$params["startfrom"] != 'last' ? 'first' : 'last';
               $params['pivot'] = !is_string($params['pivot']) ? strval($params['pivot']) : $params['pivot'];
               $params['itemperpage'] = strval($params['itemperpage']);
               $params['page'] = strval($params['page']);
               
               $result = $this->client->stateGetByIndex($this->db, $name, $params);
               if (strpos($this->indexes[$name], "json") === 0) {
                    $result = $this->formatResultAsArray($result);
               }
               
               if (!$this->manualclose) $this->close();
          } catch (Exception $e) {
               $result = null;
          }
          return $result;
     }
     
     public function del($key) {
          if (!$this->manualclose) $this->open();
          $this->client->stateDel($this->db, $key);
          if (!$this->manualclose) $this->close();
     }
}