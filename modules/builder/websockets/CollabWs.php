<?php 

class CollabWs extends WebSocketController {
    private $bc;
    private $bt; 
    
    public function init() {
        $this->bc = new State("builder-chat");
        $this->bt = new State("builder-tabs:memory");
    }
    
    // this function will be executed when 
    // there is new client connnected to websocket
    public function connected($client) {
        // $this->broadcast();
        $this->broadcast('people:' . json_encode($this->getClients(
            ['ws' => 'builder/collab']
        )));
    }
    
    // this function will be executed when 
    // client disconnected from server
    public function disconnected ($client, $reason) {
        $this->broadcast('people:' . json_encode($this->getClients(
            ['ws' => 'builder/collab']
        )));
        
        $files = $this->bt->getByKey(Yii::app()->user->id . '!tabs.list.*');
        foreach ($files as $f) {
            FileManager::close($f['p']);
        }
    }
    
    private function ask($question, $content, $from) {
        switch ($question) {
            case "who-edit":
                $online = $this->getClients(['ws' => 'builder/collab']);
                $cuid = $this->bt->get('editing|' . $content);
                $who = ['cid' => ''];
                if ($cuid != null) {
                    foreach ($online as $u) {
                        if (($u->cid . "|" . $u->uid) == $cuid) {
                            $who = $u;
                            break;
                        }
                    }
                }
                    
                $this->answer($question, $content, json_encode($who), $from);
                break;
            case "edit-by-me":
                $this->bt->set('editing|' . $content, $from['cid'] . "|" . $from['uid']);
                $this->answer($question, $content, "ok", $from);
                break;
            case "request-edit":
                $online = $this->getClients(['ws' => 'builder/collab']);
                $cuid = $this->bt->get('editing|' . $content);
                if ($cuid != null) { // someone is editing this file
                    foreach ($online as $u) {
                        if (($u->cid . "|" . $u->uid) == $cuid) {
                            $this->broadcast('request-edit:' . $content . "|" . json_encode($from), $u);
                            break;
                        }
                    }
                    $this->answer($question, $content, 'wait', $from);
                } else {
                    $this->answer($question, $content, 'ok', $from);
                }
                break;
            case "you-can-edit":
                $e = json_decode($content, true);
                $editor = $e['editor'];
                $this->bt->set('editing|' . $e['itemid'], $editor['cid'] . "|" . $editor['uid']);
                
                unset($e['editor']);
                $this->broadcast('you-can-edit:' . json_encode($e), $editor);
                $this->answer($question, $content, 'ok', $from);
            case "close-edit":
                $file = json_decode($this->bt->get($from['uid'] . '!tabs.list.' . $content), true);
                FileManager::close($file['p']);
                $this->bt->del($from['uid'] . '!tabs.list.' . $content);
                $this->bt->del('editing|' . $content);
                $this->answer($question, $content, "ok", $from);
                $this->broadcast('people:' . json_encode($this->getClients(
                    ['ws' => 'builder/collab']
                )));
                break;
        }
    }
    
    private function answer($question, $content, $answer, $from) {
        $this->broadcast('ask:'. $question . "|" . $content . "~" . $answer, $from);
    }
    
    // this function will be executed when 
    // server received new message from client
    public function received($msg, $from) {
        $msgp = explode(":", $msg);
        $type = array_shift($msgp);
        $content = implode(":", $msgp);
        switch ($type) {
            case "ask":
                $askp = explode("|", $content);
                $question = array_shift($askp);
                $content = implode("|", $askp);
                $this->ask($question, $content, $from);
                break;
            case "msg": 
                $msg = [
                    'f'=>$from,
                    't'=>time(),
                    'd'=> date("H:i:s"),
                    'm'=> $content
                ];
                $this->bc->set("m.{$msg['t']}.{$msg['f']['cid']}", $msg);
                
                $msgCache = 100; # will cache last 100 msg
                $diff = $this->bc->count() - $msgCache;
                if ($diff > 0) {
                    $all = $this->bc->getByKey('*');
                    for ($i = 0; $i < $diff; $i++) {
                        $this->bc->del($all[$i]['key']);                
                    }
                }
                
                $this->broadcast('msg:'. json_encode($msg));
                break;
            case "set":
                $content = json_decode($content, true);
                $this->bt->set($content['key'], $content['val']);
                break;
        }
    }
}
