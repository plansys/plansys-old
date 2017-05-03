<head>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.bootstrap_min'); ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.fonts'); ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.ui'); ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.default'); ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.component'); ?>
     <?php
          // include(Yii::getPathOfAlias('application.themes.flatwhite.views') . '/vendor/autoload.php');
          // $dir = Yii::getPathOfAlias('application.themes.flatwhite.views.css');
          // $stylus = new NodejsPhpFallback\Stylus($dir . "/style.styl");
          // $stylus->write($dir . "/style.css");
     ?>
     <?php Asset::registerCSS('application.themes.flatwhite.views.css.style', time()); ?>
     
     <link rel="stylesheet" href="<?= Yii::app()->controller->staticUrl('/css/font-awesome.min.css'); ?>" type="text/css" />
     <title><?php echo CHtml::encode(Yii::app()->controller->pageTitle); ?></title>
     <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">
     <?php ThemeManager::registerCoreScript(['/js/index.ctrl.js']); ?> 
     
     <?php Asset::registerJS('application.themes.flatwhite.views.js.modernizr'); ?>
     <?php Asset::registerJS('application.themes.flatwhite.views.js.dlmenu'); ?>
     <script>
          paceOptions = {
            ajax: false, // disabled
            document: true, // disabled
            eventLag: true // disabled
          };
     </script>
     <?php Asset::registerJS('application.themes.flatwhite.views.js.pace'); ?>
     
     <?php Asset::registerJS('application.themes.flatwhite.views.js.mainctrl'); ?>
     <?php //Asset::registerJS('application.themes.flatwhite.views.js.headerctrl'); ?>
</head>