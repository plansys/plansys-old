<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div>
            <div ui-header>
                <?php echo $modelDetail['modelClass']; ?><br> 
            </div>
            <div ui-content>
                <table class="table table-responsive" style="background:#f6f6f6;">
                    <tr>
                        <td><b>Source Code</b></td>
                    </tr>
                    <tr>
                        <td><span class="code"><?php echo ModelGenerator::getModelPath($class,$type);?></span></td>
                    </tr>
                    <tr>
                        <td><b>Table Name<b></td>
                    </tr>
                    <tr>
                        <td><?php echo $modelDetail['tableName'];?></td>
                    </tr>
                </table>
                <div ui-header>
                    Attributes<br> 
                </div>
                <table class="table-responsive table table-bordered">
                    <thead>
                        <tr colspan=2 style="background:#f6f6f6;">
                            <th>Column</th><th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="column in list.columns">
                            <td>{{column.name}}</td>
                            <td>{{column.dbType}} <span class="badge"  ng-if="column.isPrimaryKey == true">Primary Key</span></td>
                        </tr>
                    </tbody>
                </table>
                
                <div ui-header>
                    Rules<br> 
                </div>
                <table class="table-responsive table table-bordered">
                    <tbody>
                        <tr ng-repeat="rule in list.rules">
                            <td>{{getRule(rule)}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-warning" ng-if="list.rules.length==0">
                    <span>Rules Empty</span>
                </div>
                
                <div ui-header>
                    Relation
                </div>
                <table class="table-responsive table table-bordered">
                    <thead style="background:#f6f6f6;">
                        <th>Name</th><th>Relation </th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(key,relation) in list.relations">
                            <td>{{key}}</td>
                            <td>{{relation}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="alert alert-warning" ng-if="list.relations.length==0">
                    <span>Relations Empty</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($modelDetail);?>;
            $scope.getRule = function(item){
                var a = item.split("array(");
                a = a[1].split(")");
                return a[0];
            };

        }
    ]);
</script>