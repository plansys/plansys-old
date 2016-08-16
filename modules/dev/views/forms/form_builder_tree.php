
            <div ui-header style="padding-left:5px;">
                <i class="fa fa-file-text-o fa-nm"></i>&nbsp; Forms
            </div>
            <div ui-content id="menutree">
                <div ng-controller="FormBuilderTreeController">
                    <div oc-lazy-load="{name: 'ng-context-menu', files: [
                         '<?= Asset::publish('application.components.ui.MenuTree.ng-contextmenu.js', true); ?>'
                         ]}">
                        <div oc-lazy-load="{name: 'ui.tree', files: [
                             '<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>'
                             ]}">
                            <script type="text/ng-template" id="FormTree"><?php include('form_builder_treeitem.php'); ?></script>
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
            
            <script src="<?= Asset::publish('application.modules.dev.views.forms.form_builder_tree.js', true); ?>"></script>