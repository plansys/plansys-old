<!DOCTYPE HTML>
<html lang="en-US" ng-app="main">
<?php include("_head.php"); ?>
<body ng-controller="MainController">
    <header class="navbar navbar-fixed-top navbar-main" role="navigation">
        <div class="navbar-collapse collapse">
            <?php
            try {
                $menu = Yii::app()->controller->mainMenu;
            } catch (CdbException $e) {
                $menu = [];
            }
            MenuTree::formatMenuItems($menu);
            
            Yii::app()->controller->widget('zii.widgets.CMenu', array(
                'encodeLabel' => false,
                'id' => 'mainmenu',
                'activateParents' => true,
                'htmlOptions' => array(
                    'class' => 'nav navbar-nav'
                ),
                'submenuHtmlOptions' => array(
                    'class' => 'dropdown-menu',
                    'role' => 'nav',
                ),
                'itemCssClass' => 'dropdown',
                'itemTemplate' => '{menu}',
                'items' => $menu,
            ));
            ?>
        </div>
    </header>
    <div class="main-container" id="content" ng-cloak>
        <?php echo $content; ?>
    </div>
</body>
</html>