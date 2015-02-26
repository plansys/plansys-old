<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="<?= $this->staticUrl('/css/bootstrap.min.css'); ?>"  />
        <link rel="stylesheet" type="text/css" href="<?= $this->staticUrl('/css/non-responsive.css'); ?>"  />
        <link rel="stylesheet" type="text/css" href="<?= $this->staticUrl('/css/font-awesome.min.css'); ?>"  />
        <link rel="stylesheet" type="text/css" href="<?= $this->staticUrl('/css/main.css'); ?>"  />
        <link rel="stylesheet" type="text/css" href="<?= $this->moduleUrl . "install.css"; ?>"  />
        <title>Plansys Installer</title>
    </head>
    <body>

    <body ng-controller="MainController">
        <nav class="navbar navbar-fixed-top navbar-main" role="navigation">
            <div class="container-full">
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav" id="mainmenu">
                        <li class="no-menu dropdown-submenu"><a href="/p/plansys/index.php?r=install/#">Plansys Installer</a></li>
                    </ul>
                </div><!-- ./navbar-collapse -->
            </div><!-- /.container-full -->
        </nav>
        <div id="content" class="no-widget">
            <?php echo $content; ?>
        </div>
    </body>
</html>


