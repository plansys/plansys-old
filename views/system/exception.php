<?php

if (Setting::$mode == "init" || Setting::$mode == "install") {
    Yii::import("application.modules.install.*");
    Yii::import("application.modules.install.controllers.*");
    $module = new InstallModule("install", null);

    $controller = new DefaultController("default", $module);
    $controller->action = $controller->createAction("index");

    $msg = @$data['message'];
    if (strpos(@$data['message'], 'Application Runtime Path') === 0) {
        $msg = null;
    }
    var_dump($data);
    die();
    $controller->action->runWithParams([
        'msg' => $msg
    ]);
} else {
    Yii::import("application.controllers.*");
    $controller = new SiteController("site");
    $controller->action = $controller->createAction("error");
    $controller->action->run();

    if (!@$_GET['rendered']) {
        include(Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "exception.php");
    }
}
