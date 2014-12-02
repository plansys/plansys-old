

<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='20%' min-size="200px" class="sidebar">
            <div ui-header style="padding-left:5px;">
                <div ng-if="!loading" class="btn btn-xs pull-right btn-default"
                     ng-click="reload()" style="margin-top:3px;">Reload</div>

                <div ng-if="loading" style="float:right;margin-right:4px;">
                    Loading... 
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
    app.controller("PageController", function ($scope, $http, $localStorage) {
        $scope.list = null;
        $scope.active = null;
        $scope.select = function (item) {
            $scope.active = item.$modelValue;
            if ($scope.active.alias != null) {
                $("iframe").addClass('invisible');
                $(".loading").removeClass('invisible');
                $('.loading').removeAttr('style');
            }
        };
        $scope.is_selected = function (item) {
            if (item.$modelValue === $scope.active) {
                return "active";
            } else {
                return "";
            }
        };

        $scope.loading = false;
        $scope.reload = function () {
            $scope.loading = true;

            $http.get(Yii.app.createUrl('/dev/forms/formList')).success(function (d) {
                $scope.list = d;
                $storage.formFields = d;
                $scope.loading = false;
            });
        }

        $storage = $localStorage;
        $scope.reload();
    });

    $(document).ready(function () {
        $('iframe').on('load', function () {
            $('iframe').removeClass('invisible');
            $('.loading').addClass('invisible');
        });
    });

</script>
