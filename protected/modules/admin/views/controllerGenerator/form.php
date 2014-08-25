<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div size='60%' min-size="300px">
            <div ui-header>
                <?php echo $controller; ?><br> 
            </div>
            <div ui-content>
                <table class="table table-responsive" style="background:#f6f6f6;">
                    <tr>
                        <td><b>Source Code</b></td>
                    </tr>
                    <tr>
                        <td><span class="code"><?php echo ControllerGenerator::controllerPath($class);?></span></td>
                    </tr>
                </table>
                <div ui-header>
                    Action List
                    <div class="btn btn-xs btn-success" ng-click="new ()">
                        <i class="fa fa-plus"></i>
                        New 
                    </div>
                    
                </div>
                <div class="action-list">   
                    <?php if(empty($method)):?>
                        <div class="alert alert-warning">
                            <span>Action Empty</span>
                        </div>
                    <?php else:?>
                        <table class="table-responsive table table-bordered">
                            <thead>
                            <tr colspan=2 style="background:#f6f6f6;">
                                <th>Action Name</th><th colspan="2">Parameter</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($method as $m):?>
                            <tr>
                                <td style="width:45%;"><?php echo $m['name'];?></td>
                                <td style="width:45%;border-right-color:#ffffff;"><?php 
                                    if(!empty($m['param'])){
                                        foreach($m['param'] as $param){
                                            echo $param->name.'<br>';
                                        }
                                    }else{
                                        echo 'null';
                                    }
                                    ?>
                                </td>
                                <td style="width:10%">
                                    <?php $url = ControllerGenerator::checkUrl($class, $m['param'], $m['name'])?>
                                    <a href="#" class="glyphicon glyphicon-pencil"></a>
                                    &nbsp;
                                    <?php if($url != null):?>
                                    <a href="<?php echo Yii::app()->createUrl($url)?>" target="_blank" class="glyphicon glyphicon-eye-open"></a>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div size='40%' min-size="300px">
            <div ui-header>Properties</div>
            <div ui-content style="padding:3px 20px;">
                <div ng-show="active == null">
                    <?php include("empty.php"); ?>
                </div> 
                <div ng-show="active != null" 
                     onload="isLoading = false"
                     ng-include="Yii.app.createUrl('admin/controllerGenerator/renderProperties')"></div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode(array()); ?>;
            $scope.isLoading = true;
            $scope.activeTree = null;
            $scope.active = null;
            $scope.saving = false;
            $scope.selecting = false;
            $scope.select = function(item) {
                $scope.selecting = true;
                $scope.active = null;
                $timeout(function() {
                    $scope.active = item;
                    if (typeof $scope.active.state == "undefined") {
                        $scope.active.state = "";
                    }
                }, 0);
                console.log(item);
            };
            $scope.new = function() {
                $scope.active = null;
                $scope.list.push({
                    'name': 'actionNew',
                    'template': 'default',
                    'param': [],
                    'form': ''
                });
                $scope.save();
            };
            $scope.save = function(){
                $scope.active = $scope.list[$scope.list.length-1];
                console.log($scope.active);
                
                $http.post('<?php echo $this->createUrl("save", array('module'=>$module,'class'=>$controller)); ?>',
                        {
                            list: $scope.active   
                        })
                        .success(function(data, status) {
                            $scope.saving = false;
                            $scope.select($scope.active);
                        })
                        .error(function(data, status) {
                            $scope.saving = false;
                        });
            };     
        }
    ]);
</script>