<div  oc-lazy-load="{name: 'ng-context-menu',
    files: ['<?= Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true); ?>']}">
    <div oc-lazy-load="{name: 'ui.tree', files: [
         '<?= Yii::app()->controller->staticUrl('/js/lib/angular.ui.tree.js') ?>'
         ]}">
        <div class="tv-container" ui-tree="$ctrl.treeOptions"  data-drag-enabled="false">
            <ol ui-tree-nodes="" class="tv-root" ng-model="$ctrl.tree">
                <li ng-repeat="item in $ctrl.tree" 
                    ui-tree-node 
                    collapsed="false"
                    ng-include="$ctrl.data.itemLayoutUrl"></li>
            </ol>
        </div>
    </div>
</div>