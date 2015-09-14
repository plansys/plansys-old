function snakeToCamel(s) {
    var cm = s.replace(/(\_\w)/g, function (m) {
        return m[1].toUpperCase();
    });

    return cm.charAt(0).toUpperCase() + cm.slice(1);
}

$scope.updateTable = function () {
    $timeout(function () {
        $scope.model.modelName = snakeToCamel($scope.model.tableName);
    });
}

$scope.model.module = window.opener.activeItem.type;

if ($scope.params.href != "") {
    window.close();
    window.opener.location.href = $scope.params.href;
}