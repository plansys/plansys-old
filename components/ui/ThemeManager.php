<?php

class ThemeManager extends CThemeManager {

    public static function registerCoreScript() {
        $ctrl = Yii::app()->controller;
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/jquery.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.storage.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.lazyload.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.layout.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.bootstrap.min.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.app.js'));
        Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.ctrl.js'));
    }

}
