<?php

require_once(dirname(__FILE__) . '/../components/utility/Helper.php');
require_once(dirname(__FILE__) . '/../components/utility/Setting.php');

## Setting initialization
Setting::init(__FILE__);
Setting::initPath();
$basePath = Setting::getBasePath();
$modules = Setting::getModules();

## components
$components = array(
    'assetManager' => array(
        'basePath' => Setting::getAssetPath()
    ),
    'db' => Setting::getDB(),
    'log' => array(
        'class' => 'CLogRouter',
        'routes' => array(
            array(
                'class' => 'CFileLogRoute',
                'levels' => 'error, warning',
            ),
        ),
    ),
);

$dbLists = Setting::getDBList();
$components = $dbLists + $components;

$imports = array(
    'application.components.models.CDbCommandBuilder',
    'application.components.models.COciColumnSchema',
    'application.components.models.CMysqlColumnSchema',
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
);

foreach ($dbLists as $db=>$val) {
    array_splice($imports, 1, 0, "app.models.$db.*");
}

## define config
$config = array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Plansys Console',
    'preload' => array('log', 'EJSUrlManager'),
    'import' => $imports,
    'runtimePath' => Setting::getRuntimePath(),
    'sourceLanguage' => 'en_us',
    'language' => 'id',
    'modulePath' => Setting::getModulePath(),
    'commandMap' => Setting::getCommandMap($modules),
    'modules' => $modules,
    'components' => $components,
    'params' => array(),
);

$config = Setting::finalizeConfig($config);

return $config;
