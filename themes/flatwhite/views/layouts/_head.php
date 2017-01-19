<head>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.bootstrap_min'); ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.fonts'); ?>
     <?php
          include(Yii::getPathOfAlias('application.themes.flatwhite.views') . '/vendor/autoload.php');
          $dir = Yii::getPathOfAlias('application.themes.flatwhite.views.css');
          $stylus = new NodejsPhpFallback\Stylus($dir . "/style.styl");
          $stylus->write($dir . "/style.css");
     ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.style', time()); ?>
     
     <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
     <title><?php echo CHtml::encode(Yii::app()->controller->pageTitle); ?></title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <?php ThemeManager::registerCoreScript(['/js/index.ctrl.js']); ?> 
     <?php Asset::registerJS('application.themes.flatwhite.views.js.mainctrl'); ?>
</head>