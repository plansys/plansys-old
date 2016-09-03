<?php

class ThemeManager extends CThemeManager {

    public static function getHead() {
        $ctrl = Yii::app()->controller;
        ?>
        <head>
            <meta charset="UTF-8">
            <link rel="stylesheet" href="<?= $ctrl->staticUrl('/css/bootstrap.min.css'); ?>" type="text/css" />
            <link rel="stylesheet" href="<?= $ctrl->staticUrl('/css/non-responsive.css'); ?>" type="text/css" />
            <link rel="stylesheet" href="<?= $ctrl->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
            <link rel="stylesheet" href="<?= $ctrl->staticUrl('/css/main.css'); ?>" type="text/css" />
            <title><?php echo CHtml::encode($ctrl->pageTitle); ?></title>
            <?php
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.min.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.storage.min.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.lazyload.min.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.layout.min.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/lib/angular.ui.bootstrap.min.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.app.js'));
            Yii::app()->clientScript->registerScriptFile($ctrl->staticUrl('/js/index.ctrl.js'));
            ?> 
        </head>
        <?php
    }

}
