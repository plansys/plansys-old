$scope.contextMenu = [
    {
        icon: "fa fa-fw fa-plus",
        label: "New Controller",
        visible: function (item) {
            return !item.class;
        },
        click: function (item, e) {
            window.activeItem = item;
            PopupCenter(Yii.app.createUrl('/dev/genCtrl/newCtrl'), "Create New Controller", '400', '500');
        }
    },
    {
        icon: "fa fa-fw fa-sign-in",
        label: "Open New Tab",
        visible: function (item) {
            return !!item.class;
        },
        click: function (item) {
            window.open(item.url,'_blank');
        }
    },
    {
        icon: "fa fa-fw fa-trash",
        label: "Delete",
        visible: function (item) {
            return !!item.class;
        },
        click: function (item, e) {
            if (prompt('WARNING: THIS OPERATION CANNOT BE UNDONE\n\nType "DELETE" to delete ' + item.label + ' model') == "DELETE") {

                $http.get(Yii.app.createUrl('/dev/genCtrl/del', {
                    p: item.class
                })).success(function (data) {
                    item.$tree.remove();
                }.bind(this));
            }
        }
    }
];