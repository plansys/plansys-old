$scope.contextMenu = [
    {
        icon: "fa fa-fw fa-plus",
        label: "New Email",
        click: function (item, e) {
            window.activeItem = item;
            PopupCenter(Yii.app.createUrl('/dev/genEmail/newEmail'), "Create New Email", '350', '150');
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
        label: "Delete Email",
        visible: function (item) {
            return !!item.$parent;
        },
        click: function (item, e) {
            if (prompt('WARNING: THIS OPERATION CANNOT BE UNDONE\n\nType "DELETE" to delete ' + item.label + ' email') == "DELETE") {
                $http.get(Yii.app.createUrl('/dev/genEmail/del', {
                    p: item.class
                })).success(function (data) {
                    item.$tree.remove();
                }.bind(this));
            }
        }
    }
];