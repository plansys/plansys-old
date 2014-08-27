
<head>
    <meta charset="UTF-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/protected/static/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/protected/static/css/non-responsive.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/protected/static/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/protected/static/css/main.css'); ?>" />
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/lib/angular.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/lib/angular.ui.tree.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/protected/static/js/index.ctrl.js'));
    ?> 
</head>