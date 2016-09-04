<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/bootstrap.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/non-responsive.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
    <link rel="stylesheet" href="<?= $this->staticUrl('/css/main.css'); ?>" type="text/css" />
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <?php ThemeManager::registerCoreScript(); ?> 
</head>