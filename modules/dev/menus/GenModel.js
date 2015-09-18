$scope.contextMenu = [
    {
        icon: "fa fa-fw fa-plus",
        label: "New Model",
        click: function (item, e) {
            window.activeItem = item;
            PopupCenter(Yii.app.createUrl('/dev/genModel/newModel'), "Create New Model", '400', '500');
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
                $http.get(Yii.app.createUrl('/dev/genModel/rename', {})).success(function (status) {
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
                $http.get(Yii.app.createUrl('/dev/genModel/del', {
                    p: item.class
                })).success(function (data) {
                    item.$tree.remove();
                }.bind(this));
            }
        }
    }
];