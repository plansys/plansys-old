<?php if (@$editor): ?>
    <div ng-show="!saving" class="layout-properties-btn">
        <div class="btn btn-xs btn-default" ng-click="selectLayout('<?= $active ?>')">
            <i class="fa fa-cube fa-nm"></i>
            <span class="label label-default "><?= strtoupper($active) ?></span>
        </div>
    </div>
    <div ng-if="form.layout.name == 'dashboard'" class="layout-properties-btn" style="left:62px;">
        <a ng-href="{{ Yii.app.createUrl('/dev/forms/dashboard', {f: classPath}) }}" class="btn btn-xs btn-default" target="_blank">
            <i class="fa fa-desktop fa-nm" style="float:left;margin:3px 4px 0px 0px"></i>
            <span class="label label-default" style="font-size:11px;padding:2px 4px;margin:3px 1px 0px 0px;float:left;">
                Dashboard Mode
            </span>
        </a>
    </div>
    <div class="overlay" ng-click="unselectLayout()"></div>
<?php endif; ?>
