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
            <div id="widget-container" ng-class="{minimized:!$storage.widget.active}">
                <div id="widget-icons">
                    <div ng-class="{active:widget.isActive('notification')}" class="widget-icon"
                         ng-click="widget.toggle('notification')">
                        <i class="fa fa-newspaper-o fa-2x"></i>
                        <!--                        <div class="badge-container">
                                                    <div class="badge blink">21</div>
                                                </div>-->
                    </div>
                </div>
                <div id="widget-contents">
                    <div class="widget-notification" class="widget-content">

                        <div class = "properties-header">
                            <div class="btn btn-xs btn-success pull-right">
                                Read All
                            </div>
                            
                            <i class = "fa fa-nm fa-newspaper-o"></i>&nbsp;
                            
                            Notifications
                            
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div id="content" ng-cloak 
             <?php if (Yii::app()->user->isGuest): ?>style="right:0px;"<?php endif; ?>>
            <?php echo $content; ?>
        </div><!-- ./content -->
    </body>
</html>