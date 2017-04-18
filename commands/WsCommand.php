<?php

class WsCommand extends CConsoleCommand {
     private $server;
     
     public function beforeAction($action, $params) {
          if ($action != 'path') {
               $this->server = new WebSocketServer([
                    'tid' => $params[0],
                    'uid' => $params[1],
                    'sid' => $params[2],
                    'cid' => $params[3]
               ]);
          }
          return true;
     }
     public function afterAction($action, $params, $exitCode = 0) {
          if ($action != 'path') {
               $this->server->thrift->transport->close();
          }
          return true;
     }
     
     public function actionConnected($tid, $uid, $sid, $cid) {
          $this->server->wsctrl->connected([
               'ws' => $tid,
               'uid' => $uid,
               'sid' => $sid,
               'cid' => $cid
          ]);
          // var_dump(["CONNECTED", $tid, $uid, $sid,$cid]);
     }
     
     public function actionDisconnected($tid, $uid, $sid, $cid, $reason){
          $this->server->wsctrl->disconnected([
               'ws' => $tid,
               'uid' => $uid,
               'sid' => $sid,
               'cid' => $cid
          ], $reason);
          // var_dump(["DISCONNECTED", $tid, $uid, $sid,$cid]);
     }
     
     public function actionPath() {
          $base = parse_url(Yii::app()->request->hostInfo);
          echo json_encode([
               'wsurl' => Yii::app()->createAbsoluteUrl('/sys/ws'),
               'base' => @$base['host']
          ]);
     }
}
