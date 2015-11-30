<?php

// change the following paths if necessary
$yiic=dirname(__FILE__).'/framework/yiic.php';
$config=dirname(__FILE__).'/config/console.php';

$root = dirname(__FILE__);
if (!file_exists($root . '/vendor/autoload.php')) {
    echo "
    <center>
        <b>Composer failed to load!</b><br/>
        Please run <code>'composer update'</code> on plansys directory
    </center>";
    die();
}
$composer = require ($root . '/vendor/autoload.php');

require_once($yiic);
