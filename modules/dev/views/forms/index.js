app.controller("PageController", function ($scope, $http, $localStorage, $timeout) {
    $scope.list = actionFormList;
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
    $scope.addForm = function () {

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
                            $scope.addForm(newname, item);
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
                            $scope.addForm(newname, item);
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
                        hr: true
                    },
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
