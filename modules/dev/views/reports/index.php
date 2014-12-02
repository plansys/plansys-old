<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='17%' min-size="150px" class="sidebar">
			<div ui-header>
                Reports
            </div>
			<div ui-content oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <div ui-tree data-drag-enabled="false">
					<ol ui-tree-nodes="" ng-model="list">
						<li ng-repeat="item in list" ui-tree-node>
							<div ui-tree-handle ng-click="toggle(this);
                                            select(this);" 
                                 ng-class="is_selected(this)">

                                <div class="ui-tree-handle-info">
                                    {{item.items.length}} form{{item.items.length > 1 ? 's' : ''}}
                                </div>

                                <i ng-show="this.collapsed" class="fa fa-caret-right"></i>
                                <i ng-show="!this.collapsed" class="fa fa-caret-down"></i>

                                {{item.module}}

                            </div>
							<ol ui-tree-nodes="" ng-model="item.items">
                                <li ng-repeat="subItem in item.items" ui-tree-node>
                                    <a target="iframe" 
                                       href="<?php echo $this->createUrl('update', array()); ?>&path={{subItem.path}}{{subItem.class}}"
                                       ui-tree-handle ng-click="select(this)" ng-class="is_selected(this)">
                                        <i ng-show="!this.collapsed" class="fa fa-book fa-nm"></i>
                                        {{subItem.name}}
                                    </a>
                                </li>
                            </ol>
						</li>
					</ol>
				</div>
			</div>
		</div>
		<div style="overflow:hidden; border:0px;">
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no" seamless="seamless" name="iframe" frameborder="0" style="width:100%;height:100%;overflow:hidden;">

            </iframe>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", function($scope, $http) {
            $scope.list = <?php echo CJSON::encode($reports); ?>;
            $scope.active = null;
            $scope.select = function(item) {
                $scope.active = item.$modelValue;
            };
            $scope.is_selected = function(item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
        }
    ]);
</script>