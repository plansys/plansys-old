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