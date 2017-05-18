<?php

## Setting initialization
Setting::initPath();
$basePath = Setting::getBasePath();
$modules  = Setting::getModules();

## components
$components = array(
    'assetManager' => array(
        'basePath' => Setting::getAssetPath()
    ),
    'img'          => array(
        'class' => 'application.extensions.simpleimage.CSimpleImage',
    ),
    'ldap'         => Setting::getLDAP(),
    'user'         => array(
        'allowAutoLogin' => true,
        'class'          => 'WebUser',
    ),
    'db'           => Setting::getDB(),
    'errorHandler' => array(
        'class' => 'ErrorHandler',
    ),
    'cache'        => array(
        'class' => 'system.caching.CFileCache'
    ),
    'themeManager' => array(
        'class' => 'application.components.ui.ThemeManager',
        'basePath' => Yii::getPathOfAlias('application.themes')
    ),
    'clientScript' => array(
        'packages' => array(
            'jquery' => array(
                'basePath'           => "application.static.js.lib",
                'js'                 => array('jquery.js'),
                'coreScriptPosition' => CClientScript::POS_HEAD
            )
        )
    ),
    'log'          => array(
        'class'  => 'CLogRouter',
        'routes' => array(
        ),
    ),
    'request'      => array(
        'class'                  => 'WebRequest',
        'enableCsrfValidation'   => true,
        'enableCookieValidation' => true,
        'csrfCookie'             => array(
            'httpOnly' => true,
        ),
    ),
    'session'      => array(
        'autoStart'    => true,
        'cookieParams' => array(
            'httpOnly' => true,
        ),
    ),
);

$dbLists    = Setting::getDBList();
$components = $dbLists + $components;

if (Setting::get('app.debug') == "ON" && Setting::$mode != 'install') {
    $components['log']['routes'][] =  array(
        'class'  => 'CFileLogRoute',
        'levels' => 'error, warning',
    );
    $components['log']['routes'][] = array(
        'class'  => 'DbProfiler',
    );
    $components['log']['routes'][] = array(
        'class' => 'WebProfiler',
    );
}

$imports = array(
    'application.components.models.CDbCommand',
    'application.components.models.CDbCommandBuilder',
    'application.components.models.mysql.CMysqlColumnSchema',
    'application.components.models.oci.COciSchema',
    'application.components.models.oci.COciColumnSchema',
    'application.components.models.oci.COciCommandBuilder',
    'application.components.logging.DbProfiler',
    'application.components.logging.WebProfiler',
    'application.components.react.*',
    'application.models.*',
    'application.forms.*',
    'app.forms.*',
    'app.models.*',
    'app.components.utility.*',
    'application.components.*',
    'application.components.ui.*',
    'application.components.ui.FormFields.*',
    'application.components.utility.*',
    'application.components.models.*',
    'application.components.codegen.*',
    'application.components.repo.*',
    'application.behaviors.*',
    'application.components.HttpRequest',
    'app.components.*',
    'app.components.utility.*',
);

foreach ($dbLists as $db => $val) {
    array_splice($imports, 1, 0, "app.models.$db.*");
}

## define config
$config = array(
    'basePath'       => $basePath,
    'viewPath'       => Setting::getViewPath(),
    'name'           => (!Setting::get('app.name') ? "Plansys" : Setting::get('app.name')),
    'preload'        => array('log'),
    'import'         => $imports,
    'runtimePath'    => Setting::getRuntimePath(),
    'sourceLanguage' => 'en_us',
    'language'       => !Setting::get('app.lang') ? "id" : Setting::get('app.lang'),
    'modulePath'     => Setting::getModulePath(),
    'controllerMap'  => Setting::getControllerMap(),
    'modules'        => $modules,
    'components'     => $components,
    'params'         => array(),
);
$theme = Setting::get('app.theme');
if ($theme) {
    $config['theme'] = $theme;
}


$config = Setting::finalizeConfig($config, "main");
return $config;
