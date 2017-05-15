<?php

class ThemeManager extends CThemeManager {
    
    public static function registerCoreScript($excludeScript = []) {
        $ctrl = Yii::app()->controller;
        
        $scripts = [
            '/js/lib/jquery.js',
            '/js/lib/angular.min.js',
            '/js/lib/yii.urlmanager.min.js',
            '/js/lib/angular.storage.min.js',
            '/js/lib/angular.lazyload.min.js',
            '/js/lib/angular.ui.layout.js',
            '/js/lib/angular.ui.bootstrap.min.js',
            '/js/index.app.js',
            '/js/index.ctrl.js'
        ];
        
        $scripts = array_filter($scripts, function($val) use ($excludeScript) {
            return !in_array($val, $excludeScript); 
        });
        
        
        foreach ($scripts as $script) {
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl($script));
        }
        
        self::registerUrlManagerScript();
    }

    public static function registerUrlManagerScript() {
        $urlManager = Yii::app()->urlManager;
        $managerVars              = get_object_vars($urlManager);
        $managerVars['urlFormat'] = $urlManager->urlFormat;

        foreach ($managerVars['rules'] as $pattern => $route) {
            //Ignore custom URL classes
            if (is_array($route) && isset($route['class'])) {
                unset($managerVars['rules'][$pattern]);
            }
        }

        $encodedVars = CJSON::encode($managerVars);
        $baseUrl   = Yii::app()->getRequest()->getBaseUrl();
        $asset     = $baseUrl . '/plansys/static/js/lib';
        $scriptUrl = Yii::app()->getRequest()->getScriptUrl();
        $hostInfo  = Yii::app()->getRequest()->getHostInfo();
        Yii::app()->clientScript->registerScript(
            "yii.urlmanager",
                "var Yii = Yii || {}; Yii.app = {scriptUrl: '{$scriptUrl}',baseUrl: '{$baseUrl}',
            hostInfo: '{$hostInfo}'};
            Yii.app.urlManager = new UrlManager({$encodedVars});
            Yii.app.createUrl = function(route, params, ampersand)  {
                return typeof route == 'undefined' ? null : this.urlManager.createUrl(route, params, ampersand);};"
                , CClientScript::POS_HEAD
        );
    }

}
