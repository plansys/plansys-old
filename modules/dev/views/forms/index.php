<div ng-controller="PageController">
    <div ui-layout options="{ flow : 'column'}">
        <div size='20%' min-size="200px" class="sidebar">
            <div ui-header style="padding-left:5px;">
                <div ng-if="loading" style="float:right;margin-right:4px;">
                    Loading...
                </div>
                <i class="fa fa-file-text-o fa-nm"></i>&nbsp; Forms
            </div>
            <div ui-content>
                <div
                    oc-lazy-load="{name: 'ng-context-menu', files: ['<?= Asset::publish(Yii::getPathOfAlias('application.components.ui.MenuTree.ng-menutree') . ".js"); ?>']}">
                    <div
                        oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                        <script type="text/ng-template" id="FormTree"><?php include('form_dir.php'); ?></script>
                        <div ui-tree="treeOptions" data-drag-enabled="false">
                            <ol ui-tree-nodes="" ng-model="list">
                                <li ng-repeat="item in list" ui-tree-node collapsed="true"
                                    ng-include="'FormTree'"></li>
                            </ol>
                        </div>
                    </div>
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
<script
    src="<?= Asset::publish(Yii::getPathOfAlias('application.components.ui.MenuTree.ng-menutree') . ".js"); ?>"></script>
<script type="text/javascript">
    app.controller("PageController", function ($scope, $http, $localStorage, $timeout) {
        $scope.list = <?= $this->actionFormList() ?>;
        $scope.active = null;

        $scope.menuSelect = null;
        $scope.getType = function (sel) {
            if (!!sel.module) {
                return "module";
            }

            if (!!sel.items.length) {
                return "dir";
            }

            return "form";
        };
        $scope.executeMenu = function (func) {
            $timeout(function () {
                func($scope.menuSelect);
            });
        }
        $scope.formTreeOpen = function (sel, e, item) {
            $scope.menuSelect = sel.$modelValue;
            $(".menu-sel").removeClass("active").removeClass(".menu-sel");
            $(e.target).parent().addClass("menu-sel active");

            var type = $scope.getType(sel.$modelValue);
            switch (type) {
                case "module":
                    $scope.formTreeMenu = [
                        {
                            icon: "fa fa-fw fa-file-text-o",
                            label: "New Form",
                            click: function (item) {
                                var newname = prompt("Enter new form name:");
                            }
                        },
                        {
                            icon: "fa fa-fw fa-folder-o",
                            label: "New Folder",
                            click: function (item) {
                                var newname = prompt("Enter new folder name:");
                            }
                        }
                    ];
                    $timeout(function () {
                        $scope.select(sel, item);
                        sel.expand();
                    });
                    break;
                case "dir":
                    $scope.formTreeMenu = [
                        {
                            icon: "fa fa-fw fa-file-text-o",
                            label: "New Form",
                            click: function (item) {
                                var newname = prompt("Enter new form name:");
                            }
                        },
                        {
                            icon: "fa fa-fw fa-folder-o",
                            label: "New Folder",
                            click: function (item) {
                                var newname = prompt("Enter new folder name:");
                            }
                        },
                        {
                            hr: true
                        },
                        {
                            icon: "fa fa-fw fa-pencil",
                            label: "Rename",
                            click: function (item) {
                                var newname = prompt("Enter new name:");
                            }
                        },
                        {
                            icon: "fa fa-fw fa-sign-in",
                            label: "Move To",
                            click: function (item) {
                                alert("This feature is stil under construction...");
                            }
                        },
                        {
                            hr: true
                        },
                        {
                            icon: "fa fa-fw  fa-trash",
                            label: "Delete",
                            click: function (item) {
                                if (confirm("Delete folder \"" + item.name + "\".\nAll forms and folder under it will also be deleted.\nAre you sure?")) {
                                    return true;
                                }
                            }
                        }
                    ];
                    $timeout(function () {
                        $scope.select(sel, item);
                        sel.expand();
                    });
                    break;
                case "form":
                    $scope.formTreeMenu = [
                        {
                            icon: "fa fa-fw fa-pencil",
                            label: "Rename",
                            click: function (item) {
                                var newname = prompt("Enter new form name:");
                            }
                        },
                        {
                            icon: "fa fa-fw fa-sign-in",
                            label: "Move To",
                            click: function (item) {
                                alert("This feature is stil under construction...");
                            }
                        },
                        {
                            hr: true
                        },
                        {
                            icon: "fa fa-fw  fa-trash",
                            label: "Delete",
                            click: function (item) {
                                if (confirm("Delete form \"" + item.name + "\" ?")) {
                                    return true;
                                }
                            }
                        }
                    ];
                    break;
            }

        };

        $scope.select = function (scope, item) {
            $(".menu-sel").removeClass("active").removeClass(".menu-sel");
            $scope.active = scope.$modelValue;
            if (!!$scope.active && $scope.active.alias != null) {
                $("iframe").addClass('invisible');
                $(".loading").removeClass('invisible');
                $('.loading').removeAttr('style');
            }

            if (item && item.items && item.items.length > 0 && item.items[0].name == "Loading...") {
                $http.get(Yii.app.createUrl('/dev/forms/formList', {
                    m: item.module
                })).success(function (d) {
                    item.items = d;

                    if (typeof scope.expand == "function") {
                        scope.expand();
                    }
                });
                $storage.formBuilder.selected = {
                    module: item.module
                };
            }
        };
        $scope.init = false;
        $scope.isSelected = function (item) {
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

        $scope.treeOptions = {
            accept: function (sourceNodeScope, destNodesScope, destIndex) {
                console.log(sourceNodeScope, destNodesScope);
                return true;
            }
        };

        $timeout(function () {
            $("[ui-tree-handle].active").click();
        }, 100);
    });

    $(document).ready(function () {
        $('iframe').on('load', function () {
            $('iframe').removeClass('invisible');
            $('.loading').addClass('invisible');
        });
    });

</script>
