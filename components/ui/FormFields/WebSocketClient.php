<?php

class WebSocketClient extends FormField {
     
    public $name = '';
    public $type = 'WebSocketClient';
    public $ctrl = '';
    
    public static $toolbarName = "WebSocket Client";
    public static $category = "Data & Tables";
    public static $toolbarIcon = "fa fa-random";
    
    public function includeJS() {
        return ['ws-state.js'];
    }
    
    public function getPort() {
        $port = @file_get_contents(Yii::getPathOfAlias('webroot.assets.ports')  . ".txt");
        if (!is_null($port)) {
           $port = explode(":", $port);
           return $port[1];
        }
        return false;
    }
    
    public static function getAllControllers($isdropdown = true) {
        $ctrls = [];
        $isplansys = Setting::get('app.mode') == 'plansys';
        $root = glob(Yii::getPathOfAlias('app.websockets') . '/*Ws.php');
        foreach ($root as $file) {
            $class = substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -4);
            $path = lcfirst(substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -6));
            
            if ($path == '') continue;
            
            if ($isdropdown) {
                if ($isplansys) {
                    $ctrls['App'][$path] = $path;
                } else {
                    $ctrls[$path] = $path;
                }
            }
        }
        
        $modules = [];
        foreach (Yii::app()->modules as $k => $m) {
            if (strpos($m['class'], 'app.') === 0) {
                $modules[$k] = $m['class'];
            }
        }
        foreach ($modules as $k=>$v) {
            $glob = glob(dirname(Yii::getPathOfAlias($v)) . '/websockets/*Ws.php');
            foreach ($glob as $file) {
                $class = substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -4);
                $path = lcfirst(substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -6));
                if ($isdropdown) {
                    if ($isplansys) {
                        $ctrls['App - ' . $k][$k . '/' . $path] = $k . '/' . $path;
                    } else {
                        $ctrls[$k][$k . '/' . $path] = $k . '/' . $path;
                    }
                } else {
                    $ctrls[$k . '/' . $path] = $file;
                }
            }
        }
        
        if ($isplansys) {
            $root = glob(Yii::getPathOfAlias('application.websockets') . '/*Ws.php');
            foreach ($root as $file) {
                $class = substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -4);
                $path = lcfirst(substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -6));
                if ($path == '') continue;
                if ($isdropdown) {
                    $ctrls['Plansys'][$path] = $path;
                }
            }
            
            $modules = [];
            foreach (Yii::app()->modules as $k => $m) {
                if (strpos($m['class'], 'application.') === 0) {
                    $modules[$k] = $m['class'];
                }
            }
            
            foreach ($modules as $k=>$v) {
                $glob = glob(dirname(Yii::getPathOfAlias($v)) . '/websockets/*Ws.php');
                foreach ($glob as $file) {
                    $class = substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -4);
                    $path = lcfirst(substr(Helper::explodeLast("/", str_replace("\\","/",$file)), 0, -6));
                    if ($isdropdown) {
                        $ctrls['Plansys - ' . $k][$k . '/' . $path] = $k . '/' . $path;
                    } else {
                        $ctrls[$k . '/' . $path] = $file;
                    }
                }
            }
        }
        return $ctrls;
    }
    
    public function getFieldProperties() {
        return array (
            array (
                'label' => 'Name',
                'name' => 'name',
                'options' => array (
                    'ng-model' => 'active.name',
                    'ng-change' => 'save()',
                ),
                'type' => 'TextField',
            ),
            array (
                'label' => 'Ws Controller',
                'name' => 'ctrl',
                'options' => array (
                    'ng-model' => 'active.ctrl',
                    'ng-change' => 'save()',
                ),
                'listExpr' => 'WebSocketClient::getAllControllers()',
                'type' => 'DropDownList',
            ),
            array (
                'label' => 'New Ws Controller',
                'buttonType' => 'success',
                'icon' => 'plus',
                'position' => 'right',
                'buttonSize' => 'btn-xs',
                'options' => array (
                    'style' => 'margin-bottom:5px',
                ),
                'type' => 'LinkButton',
            ),
            array (
                'title' => 'Guide',
                'type' => 'SectionHeader',
            ),
            array (
                'renderInEditor' => 'Yes',
                'display' => 'all-line',
                'type' => 'Text',
                'value' => '<div style=\"font-family:monospace;font-size:12px;padding:10px;\">
<span style=\"white-space:pre-wrap;\">
<b>Client Connected</b>
Function to be executed when client is connected
<code style=\"display:block;\">$scope.{{active.name}}.connected(function(client) {
    // client is connected
})</code>
<b>Client Disonnected</b>
Function to be executed when client is disconnected
<code style=\"display:block;\">$scope.{{active.name}}.disconnected(function(client) {
    // client is disconnected
})</code><hr/>
<b>Send Message</b>
Send message to websocket controller
<code style=\"display:block;\">$scope.{{active.name}}.send(message)</code>
<b>Receive Message</b>
Setup listener to receive message from websocket controller
<code style=\"display:block;\">$scope.{{active.name}}.receive(function(message) {
    // message successfully received
    // from server
})</code><hr/>
</span>

</div>',
            ),
        );
    }
}