
<head>
    <meta charset="UTF-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->staticUrl('/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->staticUrl('/css/non-responsive.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->staticUrl('/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->staticUrl('/css/main.css'); ?>" />
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/afu/angular-file-upload-shim.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.tree.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/afu/angular-file-upload.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.ctrl.js'));
    ?> 
</head>