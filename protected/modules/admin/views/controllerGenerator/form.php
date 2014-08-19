<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div size='40%' min-size="300px">
            <div ui-header>
                <?php echo $class_name; ?><br> 
            </div>
            <div ui-content>
                <table class="table table-responsive" style="background:#f6f6f6;">
                    <tr>
                        <td><b>Source Code :</b></td>
                        <td><span class="code"><?php echo Yii::getPathOfAlias($class).'.php';?></span></td>
                    </tr>
                </table>
                <div class="action-list">
                    <h4>Action List</h4>
                    <?php if(empty($method)):?>
                        <div class="alert alert-warning">
                            <span>Action Empty</span>
                        </div>
                    <?php else:?>
                        <table class="table-responsive table table-bordered">
                            <thead>
                            <tr colspan=2 style="background:#f6f6f6;">
                                <th>Action Name</th><th>Parameter</th><th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($method as $m):?>
                            <tr>
                                <td><?php echo $m['name'];?></td>
                                <td><?php 
                                    if(!empty($m['param'])){
                                        foreach($m['param'] as $param){
                                            echo $param->name.'<br>';
                                        }
                                    }else{
                                        echo 'null';
                                    }
                                    ?>
                                </td>
                                <td></td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php endif;?>
                </div>
                
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($method); ?>;
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
                    $('#AdminMenuEditor\\[label\\]').focus().select();
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
                $http.post('<?php //echo $this->createUrl("save", array('class' => $path)); ?>',
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