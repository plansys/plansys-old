app.controller("IndexController", function ($scope, $http, $localStorage, $timeout) {
    $scope.list = actionFormList;
    $scope.active = null;
    $scope.editor = editor;

    $scope.menuSelect = null;
    $scope.getIcon = function (item) {
        return 'fa-file-text-o ';
    }

    $scope.getType = function (sel) {
        if (typeof sel.count != 'undefined') {
            return "module";
        }

        if (!sel.class) {
            return "dir";
        }
        return "form";
    };
    $scope.delForm = function (sel, item) {
        $http.get(Yii.app.createUrl('/dev/forms/delForm', {
            p: item.alias,
        })).success(function (data) {
            if (!!data) {
                alert(data);
            } else {
                sel.remove();
            }
        });
    };
    $scope.delFolder = function (sel, item) {
        $http.get(Yii.app.createUrl('/dev/forms/delFolder', {
            p: item.alias,
        })).success(function (data) {
            if (!!data) {
                alert(data);
            } else {
                sel.remove();
            }
        });
    };
    $scope.addForm = function (classname, extendsname, item) {

        if (!!classname && !!extendsname) {
            if (item.alias[item.alias.length - 1] == '.') {
                item.alias = item.alias.slice(0, -1);
            }
            var module = item.module.replace('Plansys: ', '');
            var params = {
                c: classname,
                e: extendsname,
                p: item.alias,
                m: module
            };
            $http.get(Yii.app.createUrl('/dev/forms/addForm', params)).success(function (data) {
                if (data) {
                    if (data.success) {
                        var sp = item.alias.split(".");
                        var shortName = data.class + "";
                        if (sp[1] == 'modules') {
                            sp = sp.splice(2);
                            if (shortName.toLowerCase().indexOf(sp[0].toLowerCase()) === 0) {
                                shortName = shortName.substr(sp[0].length);
                                sp.shift();
                            }

                            sp = sp.splice(1);
                            for (var s in sp) {
                                if (shortName.toLowerCase().indexOf(sp[s].toLowerCase()) === 0) {
                                    shortName = shortName.substr(sp[s].length);
                                }
                            }
                        }

                        item.items.push({
                            name: data.class,
                            shortName: shortName,
                            class: data.class,
                            module: item.module,
                            alias: item.alias + "." + data.class,
                            items: []
                        });
                        $scope.active = item.items[item.items.length - 1];
                        window.open(Yii.app.createUrl('dev/forms/update', {
                            'class': item.alias + "." + data.class
                        }), 'iframe');
                    } else {
                        alert(data.error);
                    }
                    return;
                }
            })
        }
    };

    $scope.addFolder = function (foldername, item) {
        if (!!foldername) {
            $http.get(Yii.app.createUrl('/dev/forms/addFolder', {
                n: foldername.toLowerCase(),
                p: item.alias
            })).success(function (data) {
                if (data) {
                    if (data.success) {
                        item.items.push({
                            alias: data.alias,
                            name: data.name,
                            module: item.module,
                            id: data.id,
                            items: []
                        });
                    } else {
                        alert(data.error);
                    }
                    return;
                }
            })
        }
    };
    $scope.executeMenu = function (e, func) {
        if (typeof func == "function") {
            $timeout(function () {
                func($scope.menuSelect);
            });
        }
    }
    window.activeScope = $scope;
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
                            $scope.activeItem = item;
                            PopupCenter(Yii.app.createUrl('/dev/forms/newForm'), "Create New Form", '400', '500');
                        }
                    },
                    {
                        icon: "fa fa-fw fa-folder-o",
                        label: "New Folder",
                        click: function (item) {
                            var foldername = prompt("Enter new folder name:");
                            $scope.addFolder(foldername, item);
                        }
                    },
                    {
                        hr: true
                    },
                    {
                        icon: "fa fa-fw fa-cube",
                        label: "New CRUD",
                        click: function (item) {
                            $scope.activeItem = item;
                            PopupCenter(Yii.app.createUrl('/dev/crud/new'), "Create New CRUD", '800', '550');
                        }
                    },
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
                            $scope.activeItem = item;
                            PopupCenter(Yii.app.createUrl('/dev/forms/newForm'), "Create New Form", '400', '500');
                        }
                    },
                    {
                        icon: "fa fa-fw fa-folder-o",
                        label: "New Folder",
                        click: function (item) {
                            var foldername = prompt("Enter new folder name:");
                            $scope.addFolder(foldername, item);
                        }
                    },
                    {
                        hr: true
                    },
                    {
                        icon: "fa fa-fw fa-cube",
                        label: "New CRUD",
                        click: function (item) {
                            $scope.activeItem = item;
                            PopupCenter(Yii.app.createUrl('/dev/crud/new'), "Create New CRUD", '800', '550');
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
                            if (confirm("Are you sure want to delete this item ?")) {
                                $scope.delFolder(sel, item);
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
                        icon: "fa fa-fw fa-sign-in",
                        label: "Open New Tab",
                        click: function (item) {
                            window.open(
                                    Yii.app.createUrl('/dev/forms/update', {
                                        'class': item.alias
                                    }),
                                    '_blank'
                                    );
                        }
                    },
                    {
                        icon: "fa fa-fw fa-pencil",
                        label: "Edit Code",
                        click: function (item) {
                            window.open(
                                    Yii.app.createUrl('/dev/forms/code', {
                                        'c': item.alias
                                    }),
                                    'iframe'
                                    );
                        }
                    },
                    {
                        hr: true
                    },
                    //{
                    //    icon: "fa fa-fw fa-pencil",
                    //    label: "Rename",
                    //    click: function (item) {
                    //        var newname = prompt("Enter new form name:");
                    //    }
                    //},
                    //{
                    //    icon: "fa fa-fw fa-sign-in",
                    //    label: "Move To",
                    //    click: function (item) {
                    //        alert("This feature is stil under construction...");
                    //    }
                    //},
                    //{
                    //    hr: true
                    //},
                    {
                        icon: "fa fa-fw  fa-trash",
                        label: "Delete",
                        click: function (item) {
                            if (confirm("Are you sure want to delete this item ?")) {
                                $scope.delForm(sel, item);
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

        if (!!$scope.active && $scope.getType($scope.active) == 'form') {
            if (typeof $scope.selectInTreeCallback == "function") {
                $scope.selectInTreeCallback();
                $scope.selectInTreeCallback = false;
            } else {
                editor.load($scope.active.alias, $scope.active.shortName);
            }
        }

        if (item && item.items && item.items.length > 0 && item.items[0].name == "Loading...") {
            $http.get(Yii.app.createUrl('/dev/forms/formList', {
                m: item.module
            })).success(function (d) {
                item.items = d;

                if (typeof scope.expand == "function") {
                    scope.expand();

                    if (typeof $scope.init == "function") {
                        $scope.init();
                    }
                }
            });
        }
    };
    $scope.isSelected = function (item) {
        if (item.$modelValue === $scope.active) {
            return "active";
        } else {
            return "";
        }
    };

    $scope.treeOptions = {
        accept: function (sourceNodeScope, destNodesScope, destIndex) {
            return true;
        }
    };

    $scope.init = true;
    $scope.editor = editor;
    $storage = $localStorage;
    $storage.formBuilder = $storage.formBuilder || {};
    $storage.modelBuilder = $storage.modelBuilder || {models:{}};

    $scope.selectInTreeCallback = false;
    editor.selectInTree = function (alias, callback) {
        if (typeof alias === "function") {
            var hash = location.hash.substr(1);
            alias = hash;
        }
        var originalAlias = alias;
        var alias = alias.split(".");
        var lastAlias = "";
        if (alias[1] == "modules") {
            lastAlias = alias[0] + "." + alias[1] + "." + alias[2] + "." + alias[3];
            alias = alias[0] + "." + alias[1] + "." + alias[2] + "." + alias[3] + ".";
        } else if (alias[1] == "components") {
            lastAlias = alias[0] + "." + alias[1] + "." + alias[2] + "." + alias[3];
            alias = alias[0] + "." + alias[1] + "." + alias[2] + "." + alias[3];
        } else {
            lastAlias = alias[0] + "." + alias[1];
            alias = alias[0] + "." + alias[1];
        }

        $scope.selectInTreeCallback = callback;

        $(".angular-ui-tree-handle.active").removeClass("active");
        $timeout(function () {
            $scope.init = function () {
                $timeout(function () {
                    var path = originalAlias.substr(lastAlias.length + 1).split(".");
                    var el = null;
                    path.forEach(function (item, idx) {
                        lastAlias += "." + item.trim();
                        el = $("li[alias='" + lastAlias + "'] > .angular-ui-tree-handle");
                        if (el.length > 0) {
                            if (el.parent().attr('collapsed') === 'true') {
                                el.click();
                            }
                        } else {
                            el = $("a[href='#" + lastAlias + "']");
                            el.click();
                        }

                        if (path.length - 1 === idx) {
                            $("#menutree").scrollTop($("#menutree").scrollTop() + el.offset().top - 80);
                        }
                    });
                    $scope.init = false;

                }, 100);
            };
            if ($("li[alias='" + alias + "']").text().replace(/\s+/ig, ' ').replace('Plansys: ', '').trim().split(" ")[1] == "Loading...") {
                $("li[alias='" + alias + "'] > .angular-ui-tree-handle").click();
            } else {
                if ($("li[alias='" + alias + "']").attr("collapsed") == 'true') {
                    $("li[alias='" + alias + "'] > .angular-ui-tree-handle").click();
                }
                $scope.init();
            }

        });
    }

    $timeout(function () {
        var hash = location.hash.substr(1);
        if (hash.length > 1) {
            editor.selectInTree(function () {
                editor.load($scope.active.alias, $scope.active.shortName);
            });
        } else {
            $scope.init = false;
        }
    }, 500);

});
