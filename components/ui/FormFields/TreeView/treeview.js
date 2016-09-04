app.component('treeView', {
    templateUrl: Yii.app.createUrl('formfield/TreeView.template'),
    controller: function ($http) {

        var self = this;
        var data = this.data;
        self.tree = ['asdsa'];
        self.treeOptions = {};

        if (!!data.initUrl) {
            $http.get(data.initUrl).success(function (res) {
                self.tree = res;
            });
        }
    },
    bindings: {
        'data': '='
    }
});