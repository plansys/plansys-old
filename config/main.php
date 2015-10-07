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
        ),
        'ePdf' => array(
            'class' => 'ext.yiipdf.EYiiPdf',
            'params' => array(
                'HTML2PDF' => array(
                    'librarySourcePath' => 'application.extensions.html2pdf.*',
                    'classFile'         => 'html2pdf.class.php', // For adding to Yii::$classMap
                    /*'defaultParams'     => array( // More info: http://wiki.spipu.net/doku.php?id=html2pdf:en:v4:accueil
                        'orientation' => 'P', // landscape or portrait orientation
                        'format'      => 'A4', // format A4, A5, ...
                        'language'    => 'en', // language: fr, en, it ...
                        'unicode'     => true, // TRUE means clustering the input text IS unicode (default = true)
                        'encoding'    => 'UTF-8', // charset encoding; Default is UTF-8
                        'marges'      => array(5, 5, 5, 8), // margins by default, in order (left, top, right, bottom)
                    )*/
                )
            ),
        ),
    ),
    'params' => array(),
);

$config = Setting::finalizeConfig($config);

return $config;
