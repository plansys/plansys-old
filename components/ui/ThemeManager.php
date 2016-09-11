<?php

class ThemeManager extends CThemeManager {

    public static function registerCoreScript() {
        $ctrl = Yii::app()->controller;
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/jquery.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/yii.urlmanager.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.storage.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.lazyload.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.layout.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.bootstrap.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.app.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.ctrl.js'));
        
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
