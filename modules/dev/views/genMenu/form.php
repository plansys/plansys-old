<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column',dividerSize:1}">
        <div ui-layout-container id="menu-drag-drop" size='40%' min-size="300px">
            <div ui-header>
                <div class="pull-right ">
                    <div ng-show="active != null;" ng-click="remove(activeTree)" class="btn btn-xs btn-danger">
                        <i class="fa fa-times"></i>
                        Delete
                    </div>
                    <div class="btn btn-xs btn-success" ng-click="new ()">
                        <i class="fa fa-plus"></i>
                        New
                    </div>
                </div>
                <i ng-if="mode == 'normal'" class="fa fa-sitemap" style="float:left;margin:6px 7px 0px  -10px;"></i>
                <?php echo $class; ?> <span ng-show='saving'>(Saving...)</span>
            </div>
            <div ui-content>
                <script type="text/ng-template" id="FormTree"><?php include('form_menu.php'); ?></script>
                <div ng-if="!isLoading"
                     oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                    <div ui-tree="listOptions" class="menu-editor">
                        <ol ui-tree-nodes ng-model="list">
                            <li data-collapsed="isCollapsed(item)" ng-repeat="item in list" ui-tree-node
                                ng-include="'FormTree'"></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div ui-layout-container id="menu-pane" size='60%' min-size="300px">
            <div ui-header>
                <i ng-if="mode == 'custom'" class="fa fa-sitemap" style="float:left;margin:6px 7px 0px  -7px;"></i>
                {{ mode == 'normal' ? 'Properties' : '<?= $class ?>'}}
                <span style="color:black;font-size:11px;" ng-if="mode == 'custom'" ng-show='saving'>
                    &nbsp; &nbsp; <span style="border:1px solid black;padding:0px 4px;border-radius:2px;"><i
                            class="fa fa-check"></i> Saving ...</span>
                </span>
                <span style="color:green;font-size:11px;display:none;" class="saved">
                    &nbsp; &nbsp; <span style="border:1px solid green;padding:0px 4px;border-radius:2px;"><i
                            class="fa fa-check"></i> Saved</span>
                </span>
                <?php
                echo FormBuilder::build('ToggleSwitch', [
                    'name' => 'mode',
                    'size' => 'small',
                    'offLabel' => 'Custom Script',
                    'onLabel' => 'Normal Menu',
                    'options' => [
                        'style' => 'float:right;width:140px;',
                        'ng-change' => 'switchMode()',
                        'ng-show' => "codeValid && !modeLocked"
                    ]
                ]);
                ?>
                <div ng-if="!codeValid" style="float:right;margin:0px 10px;color:red;">
                    <i class="fa fa-warning fa-fw"></i> Invalid Code
                </div>
                <div ng-if="modeLocked && codeValid" style="float:right;margin:0px 10px;color:#555;" tooltip="MANTA">
                    <i class="fa fa-lock fa-fw"></i> Custom Mode
                </div>

            </div>
            <div ui-content style="padding:3px 20px;">
                <div ng-if="mode == 'normal'">
                    <div ng-show="active == null">
                        <?php include("empty.php"); ?>
                    </div>
                    <div ng-show="active != null"
                         onload="isLoading = false"
                         ng-include="Yii.app.createUrl('/dev/genMenu/renderProperties')"></div>
                </div>
                <div ng-if="mode == 'custom'">
                    <div id="code-editor"
                         style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;"
                         ng-model="code"
                         ng-change="saveSource(code)"
                         ng-delay="500"
                         ui-ace="aceConfig()"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
        $scope.isLoading = true;
        $scope.listOptions = {
            dropped: function (e) {
                if (e.source.index != e.dest.index || e.source.nodesScope.$id != e.dest.nodesScope.$id) {
                    $scope.rendering = false;
                    $scope.save($scope.list);
                }
            },
            beforeDrag: function (node) {
                $scope.select(node.$handleScope);
                return true;
            }
        };
        $scope.model = {
            mode: 'Normal Menu'
        };
        $scope.mode = null;
        $scope.code = null;
        $scope.modeLocked = false;
        $scope.list = null;
        $scope.codeValid = true;
        $scope.switchModeTimeout = null;
        $scope.switchMode = function () {
            var mode = $scope.mode;
            if ($scope.model.mode == 'Normal Menu') {
                mode = "normal";
            } else {
                mode = "custom";
            }

            $http.get(Yii.app.createUrl('/dev/genMenu/switchMode', {
                path: '<?= $path ?>',
                mode: mode
            })).success(function (data) {
                $timeout(function () {
                    $scope.mode = mode;
                    $("#code-editor").html(data);
                    $scope.renderMode($scope.mode);
                }, 300);
            });
        };

        $scope.renderMode = function (mode) {
            switch (mode) {
                case "normal":
                    $scope.code = null;
                    var setCustomList = function () {
                        $("#menu-drag-drop").width('40%').show();
                        $("#menu-pane").width('60%').css('left', '40%');
                        $(".ui-splitbar").show();
                    }

                    if ($scope.list == null) {
                        $http.get(Yii.app.createUrl('/dev/genMenu/getList', {
                            path: '<?= $path ?>'
                        })).success(function (data) {
                            if (typeof data == "string") {
                                $scope.codeValid = false;
                                $scope.model.mode = 'Custom Script';
                                $scope.mode = "custom";
                                $scope.renderMode("custom");
                            } else if (typeof data == "object") {
                                $scope.list = data;
                                setCustomList();
                            }
                        });
                    } else {
                        setCustomList();
                    }
                    break;
                case "custom":
                    $scope.list = null;
                    var setCustomMode = function () {
                        $("#menu-drag-drop").width('0%').hide();
                        $("#menu-pane").width('100%').css('left', 0);
                        $(".ui-splitbar").hide();
                    };

                    if ($scope.code == null) {
                        $http.get(Yii.app.createUrl('/dev/genMenu/getCode', {
                            path: '<?= $path ?>'
                        })).success(function (data) {
                            $scope.code = data;
                            $http.get(Yii.app.createUrl('/dev/genMenu/getModeLocked', {
                                path: '<?= $path ?>'
                            })).success(function (res) {
                                $scope.modeLocked = (res == "locked");
                                setCustomMode();
                            });
                        });
                    } else {
                        setCustomMode();
                    }

                    break;
            }
        };

        // check code for error
        $("#menu-drag-drop").width('0%').hide();
        $("#menu-pane").width('100%').css('left', 0);
        $(".ui-splitbar").hide();
        $http.get(Yii.app.createUrl('/dev/genMenu/getMode', {
            path: '<?= $path ?>'
        })).success(function (data) {
            $scope.codeValid = data != '';
            $scope.mode = data != '' ? data : '';
            $scope.model.mode = $scope.mode == 'custom' ? 'Custom Script' : 'Normal Menu';
            $scope.renderMode($scope.mode);
        }).error(function () {
            $scope.codeValid = false;
            $scope.mode = 'custom';
            $scope.model.mode = 'Custom Script';
            $scope.renderMode($scope.mode);
        });

        $scope.activeTree = null;
        $scope.active = null;
        $scope.saving = false;
        $scope.selecting = false;
        $scope.rendering = false;
        $scope.select = function (item) {
            $scope.selecting = true;
            $scope.active = null;
            $scope.rendering = true;

            $timeout(function () {
                $scope.active = item.$modelValue;
                if (typeof $scope.active.state == "undefined") {
                    $scope.active.state = "";
                }

                $scope.activeTree = item;
            });

            $timeout(function () {
                $scope.rendering = false;
            }, 500);
        };
        $scope.iconAvailable = function (item) {
            if (typeof item.icon == "undefined")
                return false;
            else
                return (item.icon != '');
        }
        $scope.isCollapsed = function (item) {
            return item.state == 'collapsed' ? true : false;
        }
        $scope.isSelected = function (item) {
            if (item.$modelValue === $scope.active) {
                return "active";
            } else {
                return "";
            }
        };

        $scope.findParent = function (find, menus) {
            if (typeof menus == "undefined") {
                menus = $scope.list;
            }

            for (i in menus) {
                if (!menus[i].$$hashKey)
                    continue;

                if (menus[i].$$hashKey == find.$$hashKey) {
                    return {items: menus, index: i};
                }

                if (menus[i].items && menus[i].items.length > 0) {
                    var result = $scope.findParent(find, menus[i].items);
                    if (result != false) {
                        return result;
                    }
                }
            }
            return false;
        };

        $scope.new = function () {
            if ($scope.active) {
                var parent = $scope.findParent($scope.active);
                parent.items.splice(parent.index == 0 ? 1 : parent.index + 1, 0, {
                    'label': 'New Menu',
                    'icon': '',
                    'url': '#',
                    'items': [],
                });
            } else {
                $scope.list.push({
                    'label': 'New Menu',
                    'icon': '',
                    'url': '#',
                    'items': []
                });
            }
            $scope.save($scope.list);
        };

        $scope.remove = function (item) {
            item.remove();
            $scope.active = null;
            $scope.save($scope.list);
        };

        $scope.saveSource = function (code) {
            if (!$scope.rendering) {
                $scope.saving = true;
                $scope.list = null;
                $scope.activeTree = null;
                $scope.active = null;

                $(".saved").hide().finish();
                $http.post('<?php echo $this->createUrl("saveSource", array('class' => $path)); ?>',
                    {code: code})
                    .success(function (data, status) {
                        $scope.modeLocked = (data == "locked");

                        $scope.codeValid = true;
                        $scope.saving = false;
                        $(".saved").show().fadeOut(3000);
                    })
                    .error(function (data, status) {
                        $scope.codeValid = false;
                        $scope.saving = false;

                        $(".saved").show().fadeOut(3000);
                    });
            }
        };


        window.$(document).keydown(function (event) {
            if (!( String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19)) {
                return true;
            }
            $scope.save($scope.list);
            event.preventDefault();
            return false;
        });

        $scope.save = function (list) {
            if (!$scope.rendering) {
                $scope.saving = true;
                if ($scope.mode == 'normal') {
                    $scope.code = null;
                }
                $http.post('<?php echo $this->createUrl("save", array('class' => $path)); ?>',
                    {list: list})
                    .success(function (data, status) {
                        $scope.saving = false;
                    })
                    .error(function (data, status) {
                        $scope.saving = false;
                    });
            }
        };
    }
    ]);
</script>