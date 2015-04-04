$scope.contextMenu = [
    {
        icon: "fa fa-fw fa-plus",
        label: "New Model",
        click: function (item, e) {
            var getTableName = function (val) {
                return $.trim(val.replace(/\W+/g, " ").replace(/([A-Z])/g, function ($1) {
                    return " " + $1.toLowerCase();
                })).replace(/\s/g, '_');
            };

            var name = prompt('Please enter new model name:');
            var table = prompt('Please enter table name', getTableName(name));



            if (!!name) {
                $http.get(Yii.app.createUrl('/dev/genModel/new', {
                    name: name,
                    type: item.type,
                    table: table
                })).success(function (data) {
                    if (data.success) {
                        location.href = Yii.app.createUrl('/dev/genModel/index', {
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
        icon: "fa fa-fw fa-edit",
        label: "Rename Model",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item, e) {
            var name = prompt('Please enter new module name:', item.label);
            if (!!name) {
                $http.get(Yii.app.createUrl('/dev/genModel/rename', {
                })).success(function (status) {
                    if (status != "SUCCESS") {
                        alert(status);
                    } else {
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
        label: "Delete Model",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item, e) {
            if (prompt('WARNING: THIS OPERATION CANNOT BE UNDONE\n\nType "DELETE" to delete ' + item.label + ' model') == "DELETE") {
                $http.get(Yii.app.createUrl('/dev/genModel/delete', {
                    name: item.label,
                    module: item.module
                })).success(function (data) {
                    if (data.success) {
                        var params = {};
                        if (!!$scope.active) {
                            params.active = $scope.active.class;
                        }

                        location.href = Yii.app.createUrl('/dev/genModel/index', params);
                    } else {
                        alert(data.error);
                    }
                });
            }
        }
    }
];