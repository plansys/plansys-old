

<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='17%' min-size="150px" class="sidebar">
            <div ui-header>
                Models
            </div>
            <div ui-content>
                <script type="text/ng-template" id="FormTree"><?php include('form_dir.php'); ?></script>
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list">
                        <li ng-repeat="item in list" ui-tree-node collapsed="true" ng-include="'FormTree'">
                            
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <div style="padding:0px 0px 0px 5px;overflow:hidden;border:0px;">
            <div class="loading invisible">
                <span>
                    <b> 
                        Loading {{active.name}}... 
                    </b>
                </span>
            </div>
            <iframe src="<?php echo $this->createUrl('empty'); ?>" scrolling="no"
                    seamless="seamless" name="iframe" frameborder="0" class="invisible"
                    style="width:100%;height:100%;overflow:hidden;display:block;">

            </iframe>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", function($scope, $http) {
            $scope.list = <?php echo CJSON::encode($forms); ?>;
            $scope.active = null;
            $scope.select = function(item) {
                $scope.active = item.$modelValue;
                if ($scope.active.alias != null) {
                    $("iframe").addClass('invisible');
                    $(".loading").removeClass('invisible');
                    $('.loading').removeAttr('style');
                }
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

    $(document).ready(function() {
        $('iframe').on('load', function() {
            $('iframe').removeClass('invisible');
            $('.loading').addClass('invisible');
        });
    });

</script>
