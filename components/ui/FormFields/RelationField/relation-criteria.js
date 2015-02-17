var active = $scope.active;
$scope.$watch('active.modelClass', function (newv, oldv) {
    if (newv != oldv) {
        $scope.modelClass = newv;
    }
}, true);

$scope.modelClass = active.modelClass;