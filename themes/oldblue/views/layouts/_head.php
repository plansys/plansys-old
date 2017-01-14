<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/bootstrap.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/non-responsive.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/main.css'); ?>" type="text/css" />
    <title><?php echo CHtml::encode(Yii::app()->controller->pageTitle); ?></title>
    <?php ThemeManager::registerCoreScript(); ?> 
</head>