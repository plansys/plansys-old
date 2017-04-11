<!DOCTYPE HTML>
<html lang="en-US" ng-app="main">
<?php include("_head.php"); ?>
<body ng-controller="MainController">
    <header class="header navbar navbar-main" role="navigation">
        <div class="top-menu">
            <div class="burger">
                <i class="icon ion-navicon"></i>
            </div>
            <div class="scroll">
            <?php
            try {
                $menu = Yii::app()->controller->mainMenu;
            } catch (CdbException $e) {
                $menu = [];
            }
            
            # remove dev menu
            if (count($menu) > 2) {
                array_splice($menu,1,1);
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
            <div class="scroll-arrow" ng-if="canMenuScrollRight">
                <div class="active" ng-click="menuScrollRight()">
                    <i class="icon ion-ios-arrow-right"></i>
                </div>
            </div>
        </div>
    </header>
    <main class="main" id="content" ng-cloak>
        <?php echo $content; ?>
    </main>
</body>
</html>