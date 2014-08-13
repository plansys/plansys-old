
<head>
    <meta charset="UTF-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/css/non-responsive.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/css/main.css'); ?>" />
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/lib/angular.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/lib/angular.ui.tree.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/js/index.ctrl.js'));
    ?> 
</head>