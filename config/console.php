<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.

require_once(dirname(__FILE__) . '/../components/utility/Setting.php');
Setting::init(__FILE__);
$basePath = Setting::getBasePath();
$modules = Setting::getModules();

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Plansys Console',
    'preload' => array('log', 'EJSUrlManager'),
    // autoloading model and component classes
    'import' => array(
        'app.models.*',
        'application.models.*',
        'application.forms.*',
        'application.components.*',
        'application.components.ui.*',
        'application.components.ui.FormFields.*',
        'application.components.ui.Widgets.*',
        'application.components.utility.*',
        'application.components.models.*',
        'application.components.codegen.*',
        'application.components.repo.*',
        'application.behaviors.*',
        'ext.YiiJasper.*',
    ),
    // preloading 'log' component
    'preload' => array('log'),
    'modules' => array_merge($modules, array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => '123',
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
        'nfy'
    )),
    'aliases' => array(
        'nfy' => realpath(__DIR__ . '/../modules/nfy'),
    ),
    // application components
    'components' => array(
        'db' => Setting::getDB(),
        'nfy' => array(
            'class' => 'nfy.components.NfyDbQueue',
            'id' => 'Notifications',
            'timeout' => 30,
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
    ),
);
