$scope.contextMenu = [
    {
        icon: "fa fa-fw fa-plus",
        label: function (item, menu) {
            if (!!item.$parent) {
                return " New " + item.$parent.label + " Module";
            }

            return "New " + item.label + " Module";
        },
        click: function (item, e) {
            var name = prompt('Please enter new module name:');

            if (!!name) {
                $http.get(Yii.app.createUrl('/dev/genModule/new', {
                    name: name,
                    module: item.module
                })).success(function (data) {
                    if (data.success) {
                        location.href = Yii.app.createUrl('/dev/genModule/index', {
                            active: data.alias
                        });
                    } else {
                        alert(data.error);
                    }
                });
            }
        }
    },
    {
        icon: "fa fa-fw fa-sign-in",
        label: "Open New Tab",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item) {
            window.open(item.url,'_blank');
        }
    },
    {
        icon: "fa fa-fw fa-edit",
        label: "Rename Module",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item, e) {
            var name = prompt('Please enter new module name:', item.label);
            if (!!name) {
                $http.get(Yii.app.createUrl('/dev/genModule/rename', {
                    f: item.module + "." + item.label,
                    t: item.module + "." + name
                })).success(function (status) {
                    if (status != "SUCCESS") {
                        alert(status);
                    } else {
                        location.href = Yii.app.createUrl('/dev/genModule/index', {
                            active: item.module + "." + name,
                            gi: 1
                        });
                    }
                });
            }
        }
    },
    {
        hr: true,
        visible: function (item) {
            return !!item.$parent;
        }
    },
    {
        icon: "fa fa-fw fa-trash",
        label: "Delete Module",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item, e) {
            if (confirm('All ' + item.label.toUpperCase() + ' module files will be deleted\nAre you really sure?\n\nTHIS CANNOT BE UNDONE')) {
                if (prompt('WARNING: THIS OPERATION CANNOT BE UNDONE\n\nType "DELETE" to delete ' + item.label.toUpperCase() + ' module') == "DELETE") {
                    $http.get(Yii.app.createUrl('/dev/genModule/delete', {
                        name: item.label,
                        module: item.module
                    })).success(function (data) {
                        if (data.success) {
                            var params = {};
                            if (!!$scope.active) {
                                params.active = $scope.active.module + '.' + $scope.active.label;
                            }

                            location.href = Yii.app.createUrl('/dev/genModule/index', params);
                        } else {
                            alert(data.error);
                        }
                    });
                }
            }
        }
    }
];