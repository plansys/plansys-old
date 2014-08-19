<?php


## Setting initialization
Setting::init(__FILE__);
$basePath = Setting::getBasePath();

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
        'application.components.ui.*',
        'application.components.utility.*',
        'application.components.models.*',
        'application.components.ui.FormFields.*',
        'application.behaviors.*',
        'ext.YiiJasper.*',
        'ext.ETwigViewRenderer'
    ),
    'sourceLanguage' => 'en_us',
    'language' => 'id',
    'modules' => array(
        'admin',
        'pde',
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
            'db' => Setting::getDoctrineDB()
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
        'db' => Setting::getDB(),
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
        'widgetFactory' => array(),
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
