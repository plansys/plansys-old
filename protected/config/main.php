<?php

## get base path
$basePath = dirname(__FILE__);
$basePath = explode(DIRECTORY_SEPARATOR, $basePath);
array_pop($basePath);
$basePath = implode(DIRECTORY_SEPARATOR, $basePath);

return array(
    'basePath' => $basePath,
    'name' => 'DSI Explorasi & Penambangan',
    // preloading 'log' component
    'preload' => array('log', 'EJSUrlManager'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.forms.*',
        'application.components.*',
        'application.components.Andromedia.*',
        'application.components.Andromedia.FormFields.*',
        'application.behaviors.*',
        'ext.YiiJasper.*',
        'ext.ETwigViewRenderer'
    ),
    'sourceLanguage' => 'en_us',
    'language' => 'id',
    'modules' => array(
        'admin',
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    'aliases' => array(
        //Path to your Composer vendor dir plus vendor/bluecloudy path
        'YiiDoctrine' => realpath(__DIR__ . '/../vendor/bluecloudy/yiidoctrine2/bluecloudy/yiidoctrine2'),
    ),
    // application components
    'components' => array(
        'doctrine' => array(
            'class' => 'YiiDoctrine.components.YDComponent',
            'basePath' => $basePath,
            'proxyPath' => $basePath . '/runtime/doctrine_proxies',
            'entityPath' => array(
                $basePath . '/models'
            ),
            'cachePath' => $basePath . '/runtime/doctrine_cache',
            'db' => array(
                'driver' => 'pdo_mysql',
                'dbname' => 'plansys',
                'user' => 'root',
                'password' => 'okedeh'
            )
        ),
        'EJSUrlManager' => array(
            'class' => 'ext.JSUrlManager.EJSUrlManager'
        ),
        'curl' => array(
            'class' => 'ext.curl.Curl',
            'options' => array(CURLOPT_HEADER => true),
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'class' => 'WebUser',
        ),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=dsi',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'okedeh',
            'charset' => 'utf8',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'widgetFactory' => array(
            'widgets' => array(
                'CJuiDatePicker' => array(
                    'theme' => 'flick',
                    'themeUrl' => 'static/jui',
                ),
                'JTimePicker' => array(
                    'theme' => 'flick',
                    'themeUrl' => 'static/jui',
                    'options' => array(
                        'showPeriod' => false,
                        'minutes' => array('interval' => 15),
                    ),
                    'htmlOptions' => array('size' => 5, 'maxlength' => 5),
                ),
            ),
        ),
        'cache' => array(
            'class' => 'system.caching.CFileCache'
        ),
        'clientScript' => array(
            'packages' => array(
                'jquery' => array(
                    'basePath' => "webroot.static.js.lib",
                    'js' => array('jquery.js'),
                    'coreScriptPosition' => CClientScript::POS_HEAD
                )
            )
        )
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'webmaster@example.com',
    ),
);
