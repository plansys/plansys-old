<script type="text/javascript">
    app.controller("PageController", ["$scope", "$http", '$timeout', function ($scope, $http, $timeout) {
            $scope.list = <?php echo CJSON::encode($menus); ?>;
            $scope.active = null;
            $scope.saving = false;
            $scope.activeTree = null;
            $scope.newName = "";
            $scope.treeOptions = {
                accept: function (sourceNodeScope, destNodesScope, destIndex) {
                    console.log(sourceNodeScope, destNodesScope);
                    return true;
                }
            };
            $scope.executeMenu = function (e, func) {
                if (typeof func == "function") {
                    $timeout(function () {
                        func($scope.menuSelect);
                    });
                }
            }
            $scope.formTreeOpen = function (sel, e, item) {
                $scope.menuSelect = sel.$modelValue;
                $(".menu-sel").removeClass("active").removeClass(".menu-sel");
                $(e.target).parents('[ui-tree-handle]').addClass("menu-sel active");
                if (item.icon) {
                    $scope.formTreeMenu = [
                        {
                            icon: "fa fa-fw fa-pencil",
                            label: "Rename",
                            click: function (item) {
                                var name = prompt("Enter new menu name:", item.label);
                                if (!!name) {
                                    $http.get(Yii.app.createUrl("/dev/genMenu/renameMenu", {
                                        n: name,
                                        p: item.class
                                    })).success(function (data) {
                                        if (data.success) {
                                            item.class = data.item.class;
                                            item.label = data.item.label;
                                        } else {
                                            alert(data.error);
                                        }
                                    });
                                }
                            }
                        },
                        {
                            icon: "fa fa-fw fa-trash",
                            label: "Delete",
                            click: function (item) {
                                if (confirm('Are you sure ? ')) {
                                    $http.get(Yii.app.createUrl("/dev/genMenu/delMenu", {
                                        p: item.class
                                    })).success(function (data) {
                                        if (!!data) {
                                            alert(data);
                                        } else {
                                            sel.remove();
                                        }
                                    });
                                }
                            }
                        }
                    ];
                } else {
                    $scope.formTreeMenu = [
                        {
                            icon: "fa fa-fw fa-plus",
                            label: "New Menu",
                            click: function (item) {
                                var name = prompt("Enter new menu name:");
                                if (!!name) {
                                    var modulePath = 'app.modules.' + item.module;
                                    if (!!item.plansys) {
                                        modulePath = 'application.modules.' + item.module;
                                    }

                                    $http.get(Yii.app.createUrl("/dev/genMenu/addMenu", {
                                        n: name,
                                        m: item.module,
                                        p: modulePath
                                    })).success(function (data) {
                                        if (data.success) {
                                            item.items.push(data.item);
                                        } else {
                                            if (!data.error) {
                                                $http.get(Yii.app.createUrl("/dev/genMenu/addMenu", {
                                                    n: name,
                                                    m: item.module,
                                                    p: modulePath
                                                })).success(function (data) {
                                                    item.items.push(data.item);
                                                });
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    ];
                }
            };
            $scope.selectToggle = function (item) {
                $scope.select(item);
                item.toggle(item);
            }
            $scope.select = function (item) {
                $(".menu-sel").removeClass("active").removeClass(".menu-sel");
                $scope.activeTree = item;
                $scope.active = item.$modelValue;
            };
            $scope.rename = function (item) {
                $scope.select(item);
                item.toggle();
                $timeout(function () {
                    item.$element.find("input").focus();
                }, 0);
            };
            $scope.new = function () {
                for (var k in $scope.list) {
                    if ($scope.active.module == $scope.list[k].module) {
                        var newItemCount = $scope.list[k]['items'].length + 1;
                        $scope.saving = true;
                        module = $scope.list[k];
                        if (typeof $scope.active.class != "undefined") {
                            active = $scope.active;
                        } else {
                            active = $scope.active.items[0];
                        }

                        data = {
                            from: active.class_path + "NewMenu" + newItemCount,
                            to: "NewMenu" + newItemCount
                        };
                        $http.post('<?php echo $this->createUrl("rename"); ?>', data)
                                .success(function (data, status) {
                                    module['items'].push({
                                        name: "NewMenu" + newItemCount,
                                        module: active.module,
                                        class: active.class_path + "NewMenu" + newItemCount,
                                        class_path: active.class_path
                                    });
                                    $scope.saving = false;
                                })
                                .error(function (data, status) {
                                    $scope.saving = false;
                                });
                    }
                }
            };
            $scope.save = function (item, newName) {
                item.expand();
                if (newName.trim() == "") {
                    $scope.active.name = $scope.active.class.split(".").pop();
                    alert("New name is empty!");
                    return false;
                }

                var oldname = item.$modelValue.class.split(".").pop();
                var exist = false;
                for (var k in $scope.activeTree.$parentNodesScope.$modelValue) {
                    var ex = $scope.activeTree.$parentNodesScope.$modelValue[k];
                    var n = ex.class.split(".").pop();
                    if (n == oldname)
                        continue;
                    if (n == newName) {
                        exist = true;
                    }
                }
                if (exist) {
                    $scope.active.name = $scope.active.class.split(".").pop();
                    alert("Duplicate menu name!");
                    return false;
                }


                $scope.saving = true;
                $http.post('<?php echo $this->createUrl("rename"); ?>', {
                    from: $scope.active.class,
                    to: newName
                }).success(function (data, status) {
                    $scope.active.name = newName;
                    $scope.active.class = $scope.active.class_path + newName;
                    $scope.saving = false;
                }).error(function (data, status) {
                    $scope.saving = false;
                });
            };
            $scope.delete = function () {

                $scope.saving = true;
                $http.post('<?php echo $this->createUrl("delete"); ?>', {item: $scope.active.class})
                        .success(function (data, status) {
                            $scope.activeTree.remove();
                            $scope.active = null;
                            $scope.activeTree = null;
                            $("iframe").attr("src", "<?php echo $this->createUrl('empty'); ?>");
                            $scope.saving = false;
                        })
                        .error(function (data, status) {
                            $scope.saving = false;
                        });
            };
            $scope.is_selected = function (item) {
                if (item.$modelValue === $scope.active) {
                    return "active";
                } else {
                    return "";
                }
            };
        }
    ]);
</script>