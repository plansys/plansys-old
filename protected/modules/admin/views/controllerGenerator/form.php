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
                        <td><span class="code"><?php echo ControllerGenerator::controllerPath($class); ?></span></td>
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
                    <?php if (empty($method)): ?>
                        <div class="alert alert-warning">
                            <span>Action Empty</span>
                        </div>
                    <?php else: ?>
                        <table class="table-responsive table table-bordered">
                            <thead>
                                <tr colspan=2 style="background:#f6f6f6;">
                                    <th>Action Name</th><th colspan="2">Parameter</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in list">
                                    <td style="width:50%;">{{ item.name }}</td>
                                    <td style="width:50%;border-right-color:#ffffff;">
                                        <div class="badge" ng-repeat="param in item.param">    
                                            ${{param.name}}
                                            <span ng-if="param.default"> = {{param.default}}</span>

                                        </div>
                                        <a href="#" target="_blank" class="pull-right btn btn-default btn-xs">
                                            <i class="fa fa-globe"></i>
                                        </a>

                                        <a href="#" class="btn btn-default pull-right btn-xs" ng-click="update(item)" style="margin-right:5px;">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endif; ?>
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
                                 $scope.list = <?php echo CJSON::encode($method); ?>;
                                 $scope.edit = true;
                                 $scope.isLoading = true;
                                 $scope.activeTree = null;
                                 $scope.active = null;
                                 $scope.paramText = "";
                                 $scope.saving = false;
                                 $scope.selecting = false;
                                 $scope.select = function(item) {
                                     if (item == null)
                                         return;
                                     $scope.selecting = true;
                                     $scope.active = item;
                                     console.log(item);
                                 };
                                 $scope.new = function() {
                                     $scope.edit = true;
                                     $scope.active = {
                                         'name': 'actionNew',
                                         'template': 'default',
                                         'param': [],
                                         'form': ''
                                     };
                                 };
                                 $scope.update = function(item) {
                                     $scope.edit = false;
                                     $scope.active = {
                                         'name': item.name,
                                         'param': item.param
                                     };
                                     $scope.parseParams(item.param);
                                 };

                                 $scope.parseParams = function(param) {
                                     var parsed = [];
                                     for (i in param) {
                                         parsed.push("$" + param[i].name + " = " + param[i].default);
                                     }
                                     $scope.paramText = parsed.join(" , ");
                                     console.log($scope.paramText);
                                 };

                                 $scope.changeParam = function(paramText) {
                                     var paramArr = paramText.split(",");
                                     for (i in paramArr) {
                                         if (typeof paramArr[i] != "string") continue;
                                         
                                         var p = paramArr[i].trim().split("=");
                                         
                                         paramArr[i] = {
                                             name: p[0].trim().replace("$", '')
                                         };

                                         if (p.length > 0) {
                                             paramArr[i].default = p[1].trim();
                                         }
                                     }

                                     $scope.active.param = paramArr;

                                 };

                                 $scope.create = function() {
                                     if ($scope.edit === false) {
                                         var url = '<?php echo $this->createUrl("save", array('module' => $module, 'class' => $controller)); ?>';
                                     } else {
                                         var url = '<?php echo $this->createUrl("rename", array('module' => $module, 'class' => $controller)); ?>';
                                     }
                                     
                                     $scope.active.param = $scope.changeParam($scope.paramText);
                                     
                                     $http.post(url, {list: $scope.active}).success(function(data, status) {
                                         $scope.saving = false;
                                         $scope.list.push($scope.active);
                                         $scope.select($scope.active);
                                         $scope.edit = true;
                                     }).error(function(data, status) {
                                         $scope.saving = false;
                                     });
                                 };
                             }
                         ]);
</script>