$scope.data = {};
$scope.relations = {};
$scope.isRelated = false;
$scope.currentModel = [];
if (JSON.parse($scope.model.data)) {
    $scope.data = JSON.parse($scope.model.data);

    for (i in $scope.data) {
        if (!!$scope.data[i] && (typeof $scope.data[i] == 'object' || typeof $scope.data[i] == 'array')) {
            if (i != 'currentModel') {
                $scope.relations[i] = $scope.data[i];
                $scope.isRelated = true;
            } else {
                $scope.currentModel = $scope.data[i];
            }

            delete($scope.data[i]);
        }
    }
}
$scope.isObject = function (v) {
    return typeof v == 'object';
}

$scope.model.type = $scope.model.type.toUpperCase();