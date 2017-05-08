<?php

use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TMultiplexedProtocol;

if (!class_exists('state\Client', false)) {
     require(Yii::getPathOfAlias('application.components.thrift.client.state.Types') . ".php");
     require(Yii::getPathOfAlias('application.components.thrift.client.state.StateManager') . ".php");
}
class WebSocketServer extends CComponent {
     public $thrift;
     public $wsctrl;
     public $client;
     
     #
     # example: $server = new WebSocketServer([
     #   'tid' => 'dev/service', 
     #   'cid' => '-'
     # ])
     #
     public function __construct($client) {
          $this->client = new \state\Client([
               'tid' => @$client['tid'],
               'cid' => @$client['cid']
          ]);
          
          if (isset($client['uid']) && isset($client['sid'])) {
               $this->client->uid = $client['uid'];
               $this->client->sid = $client['sid'];
          }
          
          $ctrl = explode("/", trim($this->client->tid, '/'));
          
          $port = @file_get_contents(Setting::getRootPath() . "/assets/ports.txt");
          if (!is_null($port)) {
               $port = explode(":", $port);
          }
          
          if (count($ctrl) > 1) {
               if (substr($ctrl[1], strlen($ctrl[1]) - 2) == 'Ws') {
                    $ctrl[1] = substr($ctrl[1], 0, strlen($ctrl[1]) - 2);
               }
               
               if (isset(Yii::app()->modules[$ctrl[0]])) {
                    $path = explode(".", Yii::app()->modules[$ctrl[0]]['class']);
                    array_pop($path);
                    
                    $path = Yii::getPathOfAlias(implode(".", $path)) . "/websockets/" . ucfirst($ctrl[1]) . "Ws.php";
                    
                    if (!is_file($path)) {
                         return false;
                    }
                    $class = ucfirst($ctrl[1]) . "Ws";
                    if (!class_exists($class, false)) {
                         require($path);
                    } 
               } else {
                    return false;
               }
          } else {
               if (substr($ctrl[0], strlen($ctrl[0]) - 2) == 'Ws') {
                    $ctrl[0] = substr($ctrl[0], 0, strlen($ctrl[0]) - 2);
               }
          
               $path = Yii::getPathOfAlias("app.websockets") . "/" . ucfirst($ctrl[0]) . "Ws.php";
               if (!is_file($path)) {
                    return false;
               }
               $class = ucfirst($ctrl[0]) . "Ws";
               require($path);
          }
          
          $this->wsctrl = new $class($this);
          $this->thrift = new ServiceManager;
          try {
               $this->thrift->socket = new TSocket('127.0.0.1', @$port[0]);
               $this->thrift->transport = new TBufferedTransport($this->thrift->socket, 1024, 1024);
               $this->thrift->protocol = new TMultiplexedProtocol(new TCompactProtocol($this->thrift->transport), 'StateManager');
               $this->thrift->client = new \state\StateManagerClient($this->thrift->protocol);
               
               $this->thrift->transport->open();
          } catch (TException $tx) {
               print 'TException: '.$tx->getMessage()."\n";
          }
     }
     
     public function send($msg, $to) {
          $this->thrift->client->send(new \state\Client([
               'tid' => array_key_exists('ws', $to) ? $to['ws'] : $this->client->tid,
               'uid' => @$to['uid'] . "",
               'sid' => @$to['sid'] . "",
               'cid' => @$to['cid'] . "",
               'tag' => @$to['tag'] . ""
          ]), $msg);
     }
     
     public function getClients($client) {
          try {
               return $this->thrift->client->getClients(new \state\Client([
                    'tid' => array_key_exists('ws', $client) ? $client['ws'] : $this->client->tid,
                    'uid' => @$client['uid'] . "",
                    'sid' => @$client['sid'] . "",
                    'cid' => @$client['cid'] . "",
                    'tag' => @$client['tag'] . ""
               ]));
          } catch(Exception $e) {
               return [];
          }
          
     }
     public function setTag($client, $tag) {
          $this->thrift->client->setTag(new \state\Client([
               'tid' => array_key_exists('ws', $client) ? $client['ws'] : $this->client->tid,
               'uid' => @$client['uid'] . "",
               'sid' => @$client['sid'] . "",
               'cid' => @$client['cid'] . ""
          ]), $tag);
     }
}