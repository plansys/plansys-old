function snakeToCamel(s) {
    var cm = s.replace(/(\_\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    return cm.charAt(0).toUpperCase() + cm.slice(1);
}
if (!!window.opener.activeItem) {
    $scope.model.module = window.opener.activeItem.type;
}

$scope.updateTable = function () {
    $timeout(function () {
        $scope.model.modelName = snakeToCamel($scope.model.tableName);
    });
}

$scope.getListField = function() {
    $http.get(Yii.app.createUrl('/dev/genModel/fieldList', {'table':$scope.model.tableName}))
        .success(function(res) {
            $scope.fieldList = res;
        });
}

if ($scope.params.href != "") {
    window.close();
    window.opener.location.href = $scope.params.href;
}