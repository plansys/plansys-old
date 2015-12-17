<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/bootstrap.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/non-responsive.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/main.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticAppUrl('/css/main.css'); ?>" type="text/css" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/ocLazyLoad.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/ngStorage.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.ctrl.js'));
    ?> 
</head>