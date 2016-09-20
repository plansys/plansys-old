app.component('treeView', {
    templateUrl: Yii.app.createUrl('formfield/TreeView.template'),
    controller: function ($http) {

        var self = this;
        var data = this.data;
        self.tree = [];
        self.treeOptions = {};


        function processTree(tree) {
            for (var i in tree) {
                console.log(tree[i]);
            }
            return tree;
        }

        if (!!data.initUrl) {
            $http.get(data.initUrl).success(function (res) {
                self.tree = processTree(res);
            });
        }
    },
    bindings: {
        'data': '='
    }
});