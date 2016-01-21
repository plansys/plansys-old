<?php

## Setting initialization
Setting::initPath();
$basePath = Setting::getBasePath();
$modules = Setting::getModules();

## components
$components = array(
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
    ),
);

$dbLists = Setting::getDBList();
$components = $dbLists + $components;

$imports = array(
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
);

foreach ($dbLists as $db=>$val) {
    array_splice($imports, 1, 0, "app.models.$db.*");
}
## define config
$config = array(
    'basePath' => $basePath,
    'viewPath' => Setting::getViewPath(),
    'name' => (!Setting::get('app.name') ? "Plansys" : Setting::get('app.name')),
    'preload' => array('log', 'EJSUrlManager'),
    'import' => $imports,
    'runtimePath' => Setting::getRuntimePath(),
    'sourceLanguage' => 'en_us',
    'language' => 'id',
    'modulePath' => Setting::getModulePath(),
    'controllerMap' => Setting::getControllerMap(),
    'modules' => $modules,
    'components' => $components,
    'params' => array(),
);

$config = Setting::finalizeConfig($config);

return $config;
