<div ng-controller="PageController" ng-cloak>
    <div ui-layout class="sub" options="{ flow : 'column' }">
        <div id="menu-drag-drop" size='40%' min-size="300px">
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
                <?php echo $class; ?><span ng-show='saving'>(Saving...)</span>
            </div>
            <div ui-content>
                <script type="text/ng-template" id="FormTree"><?php include('form_menu.php'); ?></script>
                <div oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                    <div ui-tree="listOptions" class="menu-editor">
                        <ol ui-tree-nodes ng-model="list">
                            <li data-collapsed="isCollapsed(item)" ng-repeat="item in list" ui-tree-node
                                ng-include="'FormTree'"></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div id="menu-pane" size='60%' min-size="300px">
            <div ui-header>
                {{ mode == 'normal' ? 'Properties' : '<?= $class ?> Source'}}
                <?php
                echo FormBuilder::build('ToggleSwitch', [
                    'name' => 'mode',
                    'size' => 'small',
                    'offLabel' => 'Custom Script',
                    'onLabel' => 'Normal Menu',
                    'options' => [
                        'style' => 'float:right;width:145px;',
                        'ng-change' => 'switchMode()'
                    ]
                ]);
                ?>
            </div>
            <div ui-content style="padding:3px 20px;">
                <div ng-if="mode == 'normal'">
                    <div ng-show="active == null">
                        <?php include("empty.php"); ?>
                    </div>
                    <div ng-show="active != null"
                         onload="isLoading = false"
                         ng-include="Yii.app.createUrl('dev/menus/renderProperties')"></div>
                </div>
                <div ng-if="mode == 'custom'">
                    <div id="code-editor"
                         style="position:absolute;top:0px;bottom:0px;left:0px;right:0px;"
                         ng-model="code"
                         ui-ace="{
                        useWrapMode : true,
                        showGutter: true,
                        theme: 'monokai',
                        mode: 'php',
                        require: ['ace/ext/emmet'],
                        advanced: {
                        enableEmmet: true,
                        }
                    }"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", "$timeout", function ($scope, $http, $timeout) {
        $scope.list = <?php echo CJSON::encode($list); ?>;
        $scope.isDragged = false;
        $scope.isLoading = true;
        $scope.listOptions = {
            dragStop: function (node) {
                if ($scope.isDragged) {
                    $scope.save();
                    $scope.isDragged = false;
                }
            },
            dragStart: function (node) {
                $scope.isDragged = true;
            },
            beforeDrag: function (node) {
                $scope.select(node.$handleScope);
                return true;
            }
        };
        $scope.mode = "<?= $mode; ?>";
        $scope.model = {
            mode: $scope.mode == 'normal'
        };
        $scope.code = null;
        $scope.switchMode = function () {
            var mode = $scope.mode;
            if ($scope.mode == "normal") {
                mode = "custom";
            } else {
                mode = "normal";
            }
            $http.get(Yii.app.createUrl('/dev/menus/switchMode', {
                path: '<?= $path ?>',
                mode: mode
            })).success(function (data) {
                $timeout(function () {
                    $scope.mode = mode;
                    $("#code-editor").html(data);
                    $scope.renderMode($scope.mode);
                });
            });
        };
        $scope.renderMode = function (mode) {
            switch (mode) {
                case "normal":
                    $("#menu-drag-drop").width('40%').show();
                    $("#menu-pane").width('60%').css('left', '40%');
                    $(".ui-splitbar").show();
                    break;
                case "custom":
                    var setCustomMode = function () {
                        $("#menu-drag-drop").width('0%').hide();
                        $("#menu-pane").width('100%').css('left', 0);
                        $(".ui-splitbar").hide();
                    };

                    if ($scope.code == null) {
                        $http.get(Yii.app.createUrl('/dev/menus/getCode', {
                            path: '<?= $path ?>'
                        })).success(function (data) {
                            $scope.code = data;
                            setCustomMode();
                        });
                    } else {
                        setCustomMode();
                    }

                    break;
            }
        };
        $scope.renderMode($scope.mode);

        $scope.activeTree = null;
        $scope.active = null;
        $scope.saving = false;
        $scope.selecting = false;
        $scope.select = function (item) {
            $scope.selecting = true;
            $scope.active = null;
            $timeout(function () {
                $scope.active = item.$modelValue;
                if (typeof $scope.active.state == "undefined") {
                    $scope.active.state = "";
                }

                $scope.activeTree = item;
                $('#DevMenuEditor\\[label\\]').focus().select();
            }, 0);
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
        }

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
            $scope.save();
        }
        $scope.remove = function (item) {
            item.remove();
            $scope.active = null;
            $scope.save();
        }
        $scope.save = function () {
            $scope.saving = true;
            $http.post('<?php echo $this->createUrl("save", array('class' => $path)); ?>',
                {list: $scope.list})
                .success(function (data, status) {
                    $scope.code = null;
                    $scope.saving = false;
                })
                .error(function (data, status) {
                    $scope.code = null;
                    $scope.saving = false;
                });
        }
    }
    ]);
</script>