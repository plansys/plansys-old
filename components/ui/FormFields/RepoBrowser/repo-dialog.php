
<div style="display:none;" class="modal-container">
    <div tabindex="-1" role="dialog" class="modal-backdrop fade in"
         index="0" animate="animate" ng-click="close();"></div>


    <div class="modal-dialog" style="z-index:1100;">
        <div class="modal-content">
            <div class="modal-body">

                <div class="well well-sm" style="margin-bottom:5px;font-size:12px;"><i class="fa fa-folder-open-o"></i> {{ path}}</div>
                <div style="border:1px solid #ddd;">
                    <div oc-lazy-load="{name: 'ngGrid', files: [
                         '<?= Yii::app()->controller->staticUrl('/js/lib/ng-grid.debug.js') ?>'
                         , '<?= Yii::app()->controller->staticUrl('/css/ng-grid.css') ?>' ]}">
                        <div ng-if="gridReady" ng-grid="gridOptions" style="height:300px;"></div>
                    </div>
                </div>
                <div class="clearfix">
                </div>
            </div>
        </div>
    </div>
    <data name="repodata" class="hide">
        <?= json_encode(RepoManager::model()->browse(RepoManager::getModuleDir())['item']); ?>
    </data>
</div>