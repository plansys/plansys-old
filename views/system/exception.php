<?php

if (Setting::$mode == "init" || Setting::$mode == "install") {
    Yii::import("application.controllers.*");
    $controller = new InstallController("install");
    $controller->layout = "//install/_layout";
    $content = $controller->render('index', [], true);
    echo $content;
} else {
    include(Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "framework" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "exception.php");
}
