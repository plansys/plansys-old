<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div size='40%' min-size="300px">
            <div ui-header>
                <div class="pull-right ">
                    <div ng-show="active != null;" ng-click="remove(activeTree)" class="btn btn-xs btn-danger">
                        <i class="fa fa-times"></i>
                        Delete
                    </div>
                    <div class="btn btn-xs btn-success" ng-click="new ()">
                        <i class="fa fa-plus"></i>
                        New 
                    </div>
                </div>
                <?php echo $class; ?><span ng-show='saving'>(Saving...)</span>
            </div>
            <div ui-content>
                <script type="text/ng-template" id="FormTree"><?php include('form_menu.php'); ?></script>
                <div ui-tree="listOptions" class="menu-editor">
                    <ol ui-tree-nodes ng-model="list">
                        <li data-collapsed="isCollapsed(item)" ng-repeat="item in list" ui-tree-node ng-include="'FormTree'"></li>
                    </ol>
                </div>
            </div>
        </div>
        <div size='60%' min-size="300px">
            <div ui-header>Properties</div>
            <div ui-content style="padding:3px 20px;">
                <div ng-show="active == null">
                    <?php include("empty.php"); ?>
                </div> 
                <div ng-show="active != null" 
                     onload="isLoading = false"
                     ng-include="Yii.app.createUrl('dev/menus/renderProperties')"></div>
            </div>
        </div>

    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($list); ?>;
            $scope.isDragged = false;
            $scope.isLoading = true;
            $scope.listOptions = {
                dragStop: function(node) {
                    if ($scope.isDragged) {
                        $scope.save();
                        $scope.isDragged = false;
                    }
                },
                dragStart: function(node) {
                    $scope.isDragged = true;
                },
                beforeDrag: function(node) {
                    $scope.select(node.$handleScope);
                    return true;
                }
            };
            $scope.activeTree = null;
            $scope.active = null;
            $scope.saving = false;
            $scope.selecting = false;
            $scope.select = function(item) {
                $scope.selecting = true;
                $scope.active = null;
                $timeout(function() {
                    $scope.active = item.$modelValue;
                    if (typeof $scope.active.state == "undefined") {
                        $scope.active.state = "";
                    }

                    $scope.activeTree = item;
                    $('#DevMenuEditor\\[label\\]').focus().select();
                }, 0);
            };
            $scope.iconAvailable = function(item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            }
            $scope.isCollapsed = function(item) {
                return item.state == 'collapsed' ? true : false;
            }
            $scope.isSelected = function(item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
            $scope.new = function() {
                $scope.list.push({
                    'label': 'New Menu',
                    'icon': '',
                    'url': '#',
                    'items': [],
                });
                $scope.save();
            }
            $scope.remove = function(item) {
                item.remove();
                $scope.active = null;
                $scope.save();
            }
            $scope.save = function() {
                $scope.saving = true;
                $http.post('<?php echo $this->createUrl("save", array('class' => $path)); ?>',
                        {list: $scope.list})
                        .success(function(data, status) {
                            $scope.saving = false;
                        })
                        .error(function(data, status) {
                            $scope.saving = false;
                        });
            }
        }
    ]);
</script>