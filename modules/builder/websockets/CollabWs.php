<?php 

class CollabWs extends WebSocketController {
    public $state;
    
    // this function will be executed when 
    // there is new client connnected to websocket
    public function connected($client) {
        // $this->broadcast();
        $this->broadcast('people:' . json_encode($this->getClients(
            ['ws' => 'builder/collab']
        )));
        
        $this->state = new State("builderchat");
        $all = $this->state->getByKey('*');
        foreach ($all as $msg) {
            $this->broadcast('msg:' . json_encode($msg['val']), $client);
        }
    }
    
    // this function will be executed when 
    // client disconnected from server
    public function disconnected ($client, $reason) {
        $this->broadcast('people:' . json_encode($this->getClients(
            ['ws' => 'builder/collab']
        )));
    }
    
    // this function will be executed when 
    // server received new message from client
    public function received($msg, $from) {
        $msgp = explode(":", $msg);
        $type = array_shift($msgp);
        $content = implode(":", $msgp);
        switch ($type) {
            case "msg": 
                $this->state = new State("builderchat");
                
                $msg = [
                    'f'=>$from,
                    't'=>time(),
                    'd'=> date("H:i:s"),
                    'm'=> $content
                ];
                $this->state->set("m.{$msg['t']}.{$msg['f']['cid']}", $msg);
                
                $msgCache = 100; # will cache last 100 msg
                $diff = $this->state->count() - $msgCache;
                if ($diff > 0) {
                    $all = $this->state->getByKey('*');
                    for ($i = 0; $i < $diff; $i++) {
                        $this->state->del($all[$i]['key']);                
                    }
                }
                
                $this->broadcast('msg:'. json_encode($msg));
                break;
            case "set":
                $content = json_decode($content, true);
                $sb = new State("builder");
                $sb->set($content['key'], $content['val']);
                var_dump($content);
                break;
        }
    }
}
