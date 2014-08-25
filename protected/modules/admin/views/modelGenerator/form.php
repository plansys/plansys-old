<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div size='60%' min-size="300px">
            <div ui-header>
                <?php echo $modelDetail['modelClass']; ?><br> 
            </div>
            <div ui-content>
                <table class="table table-responsive" style="background:#f6f6f6;">
                    <tr>
                        <td><b>Source Code</b></td>
                    </tr>
                    <tr>
                        <td><span class="code"><?php echo ModelGenerator::getModelPath($class);?></span></td>
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
                <div ui-header>
                    Rules<br> 
                </div>
                <div ui-header>
                    Relation<br> 
                </div>
                <?php var_dump($modelDetail);?>
            </div>
        </div>
        <div size='40%' min-size="300px">

        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
                
        }
    ]);
</script>