<?php




// change the following paths if necessary
$yii = dirname ( __FILE__ ) . '/{root}/framework/yii.php';
$config = dirname ( __FILE__ ) . '/{root}/config/main.php';
$setting = dirname ( __FILE__ ) . '/{root}/components/utility/Setting.php';
$composer = require (dirname ( __FILE__ ) . '/{root}/vendor/autoload.php');

// remove the following lines when in production mode
defined ( 'YII_DEBUG' ) or define ( 'YII_DEBUG', true );
// specify how many levels of call stack should be shown in each log message
defined ( 'YII_TRACE_LEVEL' ) or define ( 'YII_TRACE_LEVEL', 3 );

// create yii app
require_once ($setting);
require_once ($yii);
Yii::createWebApplication ( $config )->run ();
