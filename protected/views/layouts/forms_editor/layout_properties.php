<?php if (@$editor): ?>
    <div ng-show="!saving" class="layout-properties-btn">
        <div class="btn btn-xs btn-default" ng-click="selectLayout('<?= $active ?>')">
            <i class="fa fa-cube fa-nm"></i>
            <span class="label label-default "><?= strtoupper($active) ?></span>
        </div>
    </div>
    <div class="overlay" ng-click="unselectLayout()"></div>
<?php endif; ?>
