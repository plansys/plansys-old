<!DOCTYPE HTML>
<html lang="en-US" ng-app="main">
<?php include("_head.php"); ?>
<body ng-controller="MainController">
<nav class="navbar navbar-fixed-top navbar-main" role="navigation">
    <div class="container-full">
        <div class="navbar-collapse collapse">
            <?php
            try {
                $menu = $this->mainMenu;
            } catch (CdbException $e) {
                $menu = [];
            }
            MenuTree::formatMenuItems($menu);

            $this->widget('zii.widgets.CMenu', array(
                'encodeLabel' => false,
                'id' => 'mainmenu',
                'activateParents' => true,
                'htmlOptions' => array(
                    'class' => 'nav navbar-nav'
                ),
                'submenuHtmlOptions' => array(
                    'class' => 'dropdown-menu',
                    'role' => 'nav'
                ),
                'itemCssClass' => 'dropdown-submenu',
                'itemTemplate' => '{menu}',
                'items' => $menu,
            ));
            ?>
        </div>
        <!-- ./navbar-collapse -->
    </div>
    <!-- /.container-full -->
</nav>
<div id="content" ng-cloak>
    <?php echo $content; ?>
</div>
</body>
</html>