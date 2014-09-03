
<div ui-tree="toolbarOptions"  style="overflow-x:hidden;">
    <ol ui-tree-nodes data-nodrop ng-model="toolbar" class="toolbar-item">
        <span ng-repeat="item in toolbar" >
            {{category(settings.category[item.type])}}
            <li ng-if="isCategory && settings.category[item.type]" class="properties-header" 
                style="width:100%;margin:5px -20px 5px -5px;padding-left:5px;color:#aaa;" >
                <i class="fa {{categorySettings[settings.category[item.type]].icon}}"></i>
                &nbsp;{{settings.category[item.type]}}
            </li>
            <li ui-tree-node ng-if="settings.category[item.type]">
                <div ui-tree-handle data-nodrop class="btn btn-default btn-sm">
                    <i class="{{settings.icon[item.type]}}" ></i>
                    {{item.name}}
                </div>
            </li>
        </span>
    </ol>
</div>

<script type="text/javascript">
    app.controller("ToolbarController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {

            var prev = "";
            $scope.isCategory = false;
            $scope.category = function(category) {
                if (prev != category) {
                    prev = category;
                    $scope.isCategory = true;
                } else {
                    $scope.isCategory = false;
                }
            }

            /*********************** TOOLBAR ***********************/
            $scope.settings = $scope.$parent.toolbarSettings;

            $scope.categorySettings = <?php echo json_encode(FormField::$categorySettings); ?>;
            $scope.toolbar = <?php echo json_encode($toolbarData); ?>;
            $scope.toolbarDefault = angular.copy($scope.toolbar);
            $scope.toolbarOptions = {
                accept: function(sourceNodeScope, destNodesScope, destIndex) {
                    return false;
                },
                dragStart: function(scope) {
                    scope.elements.placeholder.replaceWith(scope.elements.dragging.clone().find('li'));
                },
                dragStop: function(scope) {
                    $timeout(function() {
                        // auto select dropped toolbar
                        $scope.toolbar = $scope.toolbarDefault;
                        $scope.toolbarDefault = angular.copy($scope.toolbar);
                        $(scope.dest.nodesScope.$element).find(".form-field:eq(" + scope.dest.index + ")").click();
                        var model = scope.dest.nodesScope.$modelValue[scope.dest.index];

                        // generate model name
                        if (typeof model.name != "undefined") {
                            model.name = model.name.charAt(0).toLowerCase() + model.name.slice(1); // letter first letter
                            model.name = model.name.replace(/\s/, "") + "" + $(".form-builder ." + model.type).length;
                        }

                        // action bar should always be placed on first array
                        if (model.type == 'ActionBar') {
                            var clone = angular.copy(scope.dest.nodesScope.$modelValue[scope.dest.index]);
                            scope.dest.nodesScope.$modelValue.splice(scope.dest.index, 1);
                            $scope.$parent.fields.unshift(clone);
                        }

                        // save it
                        $scope.$parent.save();
                    }, 0);
                }
            };
        }
    ]);
</script>
