$scope.getCellTemplate = function (item, idx) {
    var cellTemplateUrl = Yii.app.createUrl('/formfield/GridView.cellTemplate');
    var cellTemplateData = {
        item: item,
        class: $scope.classPath,
        name: $scope.active.name,
        idx: idx
    }

    $http.post(cellTemplateUrl, cellTemplateData).then(function (res) {
        item.html = res.data;
    });
}

$scope.formatColName = function(item) {
    $timeout(function() {
        this.value = this.value.replace(/[\W]+/g,"_");
        item.name = this.value;
        $timeout(function() {
            $scope.updateListView();
        }, 500);
    }.bind(this));
}