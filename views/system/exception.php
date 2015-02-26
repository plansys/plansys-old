<?php

if (Setting::$mode == "init" || Setting::$mode == "install") {
    Yii::import("application.modules.install.*");
    Yii::import("application.modules.install.controllers.*");
    $module = new InstallModule("install", null);
    
    $controller = new DefaultController("default", $module);
    $controller->action = $controller->createAction("index");
    $controller->action->runWithParams([
        'msg' => $data['message']
    ]);
} else {
    include(Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "exception.php");
}
