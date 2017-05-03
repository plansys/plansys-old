<?php

use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TCompactProtocol;
use Thrift\Protocol\TMultiplexedProtocol;

class WsController extends Controller { 
     public $server;
     public $enableCsrf = false;
     
     public function beforeAction($action) {
          if (!Yii::app()->user->isGuest) {
               $_GET['uid'] = Yii::app()->user->id;
               $_GET['sid'] = Yii::app()->getSession()->getSessionId();
          } else {
               $_GET['uid'] = null;
               $_GET['sid'] = Yii::app()->getSession()->getSessionId();
          }
          
          $this->server = new WebSocketServer($_GET);
          return true;
     }
     
     public function afterAction($action) {
          $this->server->thrift->transport->close();
          return true;
     }
     
     public function actionSend() {
          $msg = file_get_contents("php://input");
          $this->server->wsctrl->received($msg, [
               'uid' => $this->server->client->uid,
               'sid' => $this->server->client->sid,
               'cid' => $this->server->client->cid
          ]);
     }
     
     public function actionStag() {
          $post = $_GET;
          $this->server->setTag([
               'tid' => @$post['tid'],
               'uid' => @$post['uid'],
               'sid' => @$post['sid'],
               'cid' => @$post['cid'],
          ], @$post['tag']);
     }
}