
<head>
    <meta charset="UTF-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php
    Yii::app()->clientScript->registerCoreScript('jquery');
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/afu/angular-file-upload-shim.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.layout.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.tree.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/angular.ui.bootstrap.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/ng-grid.debug.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/ngStorage.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/lib/afu/angular-file-upload.min.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.app.js'));
    Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/index.ctrl.js'));
	Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/highcharts/highcharts.js'));
	Yii::app()->clientScript->registerScriptFile($this->staticUrl('/js/colorpicker/js/colorpicker.js'));
	
    
    Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/bootstrap.min.css'));
    Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/non-responsive.css'));
    Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/font-awesome.min.css'));
    Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/ng-grid.css'));
    Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/main.css'));
	Yii::app()->clientScript->registerCSSFile($this->staticUrl('/css/colorpicker/css/colorpicker.css'));
    ?> 
</head>