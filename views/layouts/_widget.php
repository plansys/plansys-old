<div id="widget-data" class="hide"><?= json_encode(Widget::listActiveWidget()); ?></div>
<div id="widget-container" ng-show="$storage.widget.list" ng-cloak ng-class="{maximized:$storage.widget.active}">
    <div id="widget-icons">
        <div ng-repeat="w in $storage.widget.list" ng-class="{active:widget.isActive(w.class)}" class="widget-icon"
             ng-click="widget.toggle(w.class)">
            <i class="{{w.widget.icon}}"></i>

            <div ng-if="w.widget.badge != ''" class="badge-container">
                <div class="badge">{{ w.widget.badge}}</div>
            </div>
        </div>
    </div>
    <div id="widget-contents">
        <?php foreach (Widget::listActiveWidget() as $w): ?>
            <div ng-show="widget.isActive('<?= $w['class']; ?>')" 
                 class="widget-content widget-<?= $w['class']; ?>" class="widget-content">
                <?= $w['widget']->render(); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
