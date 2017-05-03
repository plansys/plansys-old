<?php 

class ServiceWs extends WebSocketController {

    // this function will be executed when 
    // there is new client connnected to websocket
    public function connected($client) {
    }
    
    // this function will be executed when 
    // client disconnected from server
    public function disconnected ($client, $reason) {
        
    }
    
    // this function will be executed when 
    // server received new message from client
    public function received($msg, $from) {
    }
}
