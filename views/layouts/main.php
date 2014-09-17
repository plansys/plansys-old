<!DOCTYPE HTML>
<html lang="en-US" ng-app="main">
    <?php include("_head.php"); ?>
    <body ng-controller="MainController">
        <nav class="navbar navbar-fixed-top navbar-main" role="navigation">
            <div class="container-full">
                <div class="navbar-collapse collapse">
                    <?php
                    $menu = $this->mainMenu;
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
                </div><!-- ./navbar-collapse -->
            </div><!-- /.container-full -->
        </nav>
        <?php if (!Yii::app()->user->isGuest): ?>

            <div id="widget-data" class="hide"><?= json_encode(Widget::listActiveWidget()); ?></div>
            <div id="widget-container" ng-cloak ng-class="{maximized:$storage.widget.active}">
                <div id="widget-icons">
                    <div ng-repeat="w in $storage.widget.list" ng-class="{active:widget.isActive(w.class)}" class="widget-icon"
                         ng-click="widget.toggle(w.class)">
                        <i class="{{w.widget.icon}}"></i>
                        
                        <div ng-if="w.widget.badge != ''" class="badge-container">
                            <div class="badge blink">{{ w.widget.badge }}</div>
                        </div>
                    </div>
                </div>
                <div id="widget-contents">
                    <?php foreach (Widget::listActiveWidget() as $w): ?>
                    <div class="widget-<?= $w['class']; ?>" class="widget-content">
                        <?= $w['widget']->render(); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div id="content" ng-cloak 
             <?php if (Yii::app()->user->isGuest): ?>style="right:0px;"<?php endif; ?>>
            <?php echo $content; ?>
        </div><!-- ./content -->
    </body>
</html>