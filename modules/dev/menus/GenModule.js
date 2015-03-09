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
    },
    {
        hr: true
    },
    {
        icon: "fa fa-fw fa-trash",
        label: "Delete Module",
        click: function (item, e) {
            if (prompt('Type "DELETE" to delete [' + item.label + '] module')) {
                if (confirm('All [' + item.label + '] module files will be deleted\nAre you really sure?\n\nTHIS CANNOT BE UNDONE')) {
                    console.log(item);
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