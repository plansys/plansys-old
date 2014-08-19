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
                <div ui-header>Action List</div>
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
                                    <a href="#" class="glyphicon glyphicon-eye-open"/>&nbsp;
                                    <a href="#" class="glyphicon glyphicon-pencil"/>
                                </td>
                            </tr>
                            <?php endforeach;?>
                            </tbody>
                        </table>
                    <?php endif;?>
                </div>
                <div ui-header>Access Role</div>
                <div class="action-list">
                    <div class="alert alert-warning">
                        <span>Access Role Empty</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function($scope, $http, $timeout) {
        }
    ]);
</script>