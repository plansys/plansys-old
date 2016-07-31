<?php Yii::import('application.components.utility.Asset'); ?> 
<div>
    <div ui-layout options="{ flow : 'column',dividerSize:1}">
        <div ui-layout-container size='20%' min-size="200px" class="sidebar">
            <div ui-header style="padding-left:5px;">
                <i class="fa fa-file-text-o fa-nm"></i>&nbsp; Forms
            </div>
            <div ui-content id="menutree">
                <div ng-controller="IndexController">
                    <div oc-lazy-load="{name: 'ng-context-menu', files: [
                         '<?= Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true); ?>'
                         ]}">
                        <div oc-lazy-load="{name: 'ui.tree', files: [
                             '<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>'
                             ]}">
                            <script type="text/ng-template" id="FormTree"><?php include('index_tree.php'); ?></script>
                            <div ui-tree="treeOptions" data-drag-enabled="false">
                                <ol ui-tree-nodes="" ng-model="list">
                                    <li ng-repeat="item in list" ui-tree-node collapsed="true" alias="{{item.alias}}"
                                        ng-include="'FormTree'"></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div ui-layout-container size='80%'>
            <?php include("index_tabs.php"); ?>
        </div>
    </div>
</div>
<script>
    var actionFormList = <?= $this->actionFormList() ?>;
    var editor = {
        formBuilder: {types: {}},
        modelBuilder: {models: {}}
    };
    window.csrf = {
        name: "<?php echo Yii::app()->request->csrfTokenName; ?>",
        token: "<?php echo Yii::app()->request->csrfToken; ?>"
    };
</script>
<script src="<?= Asset::publish('application.modules.dev.views.forms.index.js', true); ?>"></script>
