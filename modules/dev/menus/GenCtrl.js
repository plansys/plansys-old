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
    //{
    //    icon: "fa fa-fw fa-edit",
    //    label: "Rename Ctrl",
    //    visible: function (item) {
    //        return !!item.$parent;
    //    },
    //    click: function (item, e) {
    //        var name = prompt('Please enter new module name:', item.label);
    //        if (!!name) {
    //            $http.get(Yii.app.createUrl('/dev/genCtrl/rename', {})).success(function (status) {
    //                if (status != "SUCCESS") {
    //                    alert(status);
    //                } else {
    //                }
    //            });
    //        }
    //    }
    //},
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