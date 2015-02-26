

<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='20%' min-size="200px" class="sidebar">
            <div ui-header style="padding-left:5px;">
                <div ng-if="loading" style="float:right;margin-right:4px;">
                    Loading... 
                </div>
                <div ng-if="!loading" class="btn btn-default btn-xs pull-right" style="margin:4px 4px 0px 0px;">
                    <i class="fa fa-plus"></i> New
                </div>
                <i class="fa fa-file-text-o fa-nm"></i>&nbsp; Forms
            </div>
            <div ui-content oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                <script type="text/ng-template" id="FormTree"><?php include('form_dir.php'); ?></script>
                <div ui-tree data-drag-enabled="false">
                    <ol ui-tree-nodes="" ng-model="list">
                        <li ng-repeat="item in list" ui-tree-node collapsed="true" ng-include="'FormTree'"></li>
                    </ol>
                </div>
            </div>
        </div>
        <div style="padding:0px 0px 0px 1px;overflow:hidden;border:0px;">
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
    app.controller("PageController", function($scope, $http, $localStorage, $timeout) {
        $scope.list = <?= $this->actionFormList() ?>;
        $scope.active = null;
        $scope.select = function(scope, item) {
            $scope.active = scope.$modelValue;
            if (!!$scope.active && $scope.active.alias != null) {
                $("iframe").addClass('invisible');
                $(".loading").removeClass('invisible');
                $('.loading').removeAttr('style');
            }
            if (item && item.items && item.items.length > 0 && item.items[0].name == "Loading...") {
                $http.get(Yii.app.createUrl('/dev/forms/formList', {
                    m: item.module
                })).success(function(d) {
                    item.items = d;
                });
                $storage.formBuilder.selected = {
                    module: item.module
                };
            }
        };
        $scope.init = false;
        $scope.is_selected = function(item) {
            var s = $storage.formBuilder.selected;
            var m = item.$modelValue;
            if (!!s && !!m && !$scope.active && m.module == s.module) {
                $scope.init = true;
                return "active";
            }

            if (item.$modelValue === $scope.active) {
                return "active";
            } else {
                return "";
            }
        };

        $scope.loading = false;
        $storage = $localStorage;
        $storage.formBuilder = $storage.formBuilder || {};

        $timeout(function() {
            $("[ui-tree-handle].active").click();
        }, 100);
    });

    $(document).ready(function() {
        $('iframe').on('load', function() {
            $('iframe').removeClass('invisible');
            $('.loading').addClass('invisible');
        });
    });

</script>
