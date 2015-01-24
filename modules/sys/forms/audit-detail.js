$scope.data = {};
$scope.relations = {};
if (JSON.parse($scope.model.data)) {
    $scope.data = JSON.parse($scope.model.data);

    for (i in $scope.data) {
        if (typeof $scope.data[i] != 'string' && typeof $scope.data[i] != 'boolean') {
            $scope.relations[i] = $scope.data[i];
            delete($scope.data[i]);
        }
    }
}

$scope.model.type = $scope.model.type.toUpperCase();