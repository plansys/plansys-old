<?php

// change the following paths if necessary
$yiic=dirname(__FILE__).'/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

$root = dirname(__FILE__);
if (!file_exists($root . '/vendor/autoload.php')) {
    echo "
    Composer failed to load!<Br/>
    Please run 'composer update' on plansys directory
    ";
    die();
}
$composer = require ($root . '/vendor/autoload.php');

if (is_file($root . '/../app/vendor/autoload.php')) {
    $composerApp = require ($root . '/../app/vendor/autoload.php');
}

require_once($yiic);
