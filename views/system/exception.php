<?php

if (Setting::$mode == "init" || Setting::$mode == "install") {
    Yii::import("application.modules.install.*");
    Yii::import("application.modules.install.controllers.*");
    $module = new InstallModule("install", null);
    
    $controller = new DefaultController("default", $module);
    $controller->action = $controller->createAction("index");

    if (strpos($data['msg'], 'Application Runtime Path') === 0) {
        $msg = null;
    }

    var_dump($data);
    die();
    
    echo "ASDASD";
    die();
    
    $controller->action->runWithParams([
        'msg' => $msg
    ]);
} else {
    include(Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "exception.php");
}
