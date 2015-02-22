<?php

## Setting initialization
Setting::initPath();
$basePath = Setting::getBasePath();
$modules = Setting::getModules();

## define config
$config = array(
    'basePath' => $basePath,
    'name' => (!Setting::get('app.name') ? "Plansys" : Setting::get('app.name')),
    'preload' => array('log', 'EJSUrlManager'),
    'import' => array(
        'app.models.*',
        'application.models.*',
        'application.forms.*',
        'app.forms.*',
        'app.components.utility.*',
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
    'runtimePath' => Setting::getRuntimePath(),
    'sourceLanguage' => 'en_us',
    'language' => 'id',
    'modulePath' => Setting::getModulePath(),
    'controllerMap' => Setting::getControllerMap(),
    'modules' => array_merge($modules, array(
        'nfy' => array(
            'class' => 'nfy.NfyModule'
        )
    )),
    'aliases' => array(
        'nfy' => Setting::getBasePath() . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'nfy',
    ),
    'components' => array(
        'assetManager' => array(
            'basePath' => Setting::getAssetPath()
        ),
        'img' => array(
            'class' => 'application.extensions.simpleimage.CSimpleImage',
        ),
        'ldap' => Setting::getLDAP(),
        'nfy' => array(
            'class' => 'nfy.components.NfyDbQueue',
            'id' => 'Notifications',
            'timeout' => 30,
        ),
        'todo' => array(
            'class' => 'application.components.ui.Widgets.TodoWidget',
        ),
        'EJSUrlManager' => array(
            'class' => 'ext.JSUrlManager.EJSUrlManager'
        ),
        'user' => array(
            'allowAutoLogin' => true,
            'class' => 'WebUser',
        ),
        'db' => Setting::getDB(),
        'errorHandler' => array(
            'class' => 'ErrorHandler',
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
                    'basePath' => "application.static.js.lib",
                    'js' => array('jquery.js'),
                    'coreScriptPosition' => CClientScript::POS_HEAD
                )
            )
        )
    ),
    'params' => array(),
);

return Setting::finalizeConfig($config);

