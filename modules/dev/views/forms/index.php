<?php 
Yii::import('application.components.utility.Asset');
?> 

<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column',dividerSize:1}">
        <div ui-layout-container size='20%' min-size="200px" class="sidebar">
            <div ui-header style="padding-left:5px;">
                <div ng-if="loading" style="float:right;margin-right:4px;">
                    Loading...
                </div>
                <i class="fa fa-file-text-o fa-nm"></i>&nbsp; Forms
            </div>
            <div ui-content>
                <div oc-lazy-load="{name: 'ng-context-menu', files: [
                     '<?= Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true); ?>'
                     ]}">
                    <div oc-lazy-load="{name: 'ui.tree', files: [
                         '<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>'
                         ]}">
                        <script type="text/ng-template" id="FormTree"><?php include('form_menutree.php'); ?></script>
                        <div ui-tree="treeOptions" data-drag-enabled="false">
                            <ol ui-tree-nodes="" ng-model="list">
                                <li ng-repeat="item in list" ui-tree-node collapsed="true"
                                    ng-include="'FormTree'"></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div ui-layout-container style="padding:0px 0px 0px 1px;overflow:hidden;border:0px;">
            <div class="loading invisible">
                <span>
                    <b>
                        <i class="fa fa-refresh fa-spin"></i>  Loading {{active.name}}...
                    </b>
                </span>
            </div>
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no"
                    seamless="seamless" name="iframe" frameborder="0" class="invisible"
                    style="width:100%;height:100%;overflow:hidden;display:block;">
            </iframe>
        </div>
    </div>
</div>
<script>var actionFormList = <?= $this->actionFormList() ?>;</script>
<script src="<?= Asset::publish('application.modules.dev.views.forms.index.js', true); ?>"></script>
