<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column',dividerSize:1}">
        <div ui-layout-container size='17%' min-size="150px" class="sidebar">
            <div ui-header>
                <!-- Prefered way to add/delete menu is by directly editing the file
                <div class="pull-right">
                    <div ng-show="active != null && active.items == null && active.name != 'MainMenu'"  ng-click="delete()" class="btn btn-xs btn-danger">
                        <i class="fa fa-times"></i>
                        Del
                    </div>
                    <div class="btn btn-xs btn-success" ng-show="active != null" ng-click="new ()">
                        <i class="fa fa-plus"></i>
                        New 
                    </div>
                </div>
                -->
                <span ng-show='!saving'>Menus</span>
                <span ng-show='saving'>Saving...</span>
            </div>
            <div ui-content>
                <div oc-lazy-load="{name: 'ng-context-menu', files: [
                     '<?= Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true); ?>'
                     ]}">
                    <div oc-lazy-load="{name: 'ui.tree', files: [
                         '<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>'
                         ]}">
                        <div ui-tree="treeOptions" data-drag-enabled="false">
                            <script type="text/ng-template" id="FormTree"><?php include('form_menutree.php'); ?></script>
                            <ol ui-tree-nodes ng-model="list">
                                <li ng-repeat="item in list" ui-tree-node collapsed="true"
                                    ng-include="'FormTree'"></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div ui-layout-container style="overflow:hidden;border:0px;">
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no" seamless="seamless" name="iframe" frameborder="0" style="width:100%;height:100%;overflow:hidden;">

            </iframe>
        </div>
    </div>
</div>
<?php include('index.js.php'); ?>
