
<head>
    <meta charset="UTF-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/plansys/css/bootstrap.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/plansys/css/non-responsive.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/plansys/css/font-awesome.min.css'); ?>" />
    <link rel="stylesheet" type="text/css" href="<?php echo $this->url('/static/plansys/css/main.css'); ?>" />
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/lib/angular.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/lib/angular.ui.tree.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->url('/static/plansys/js/index.ctrl.js'));
    ?> 
</head>