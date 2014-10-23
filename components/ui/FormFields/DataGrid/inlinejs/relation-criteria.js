$scope.$watch('item.relModelClass', function (newv, oldv) {
    if (newv != oldv) {
        $scope.modelClass = newv;
        $scope.value = $scope.item.relCriteria;
    }
}, true);
$scope.modelClass = $scope.item.relModelClass;
$scope.value = $scope.item.relCriteria;
$scope.$watch('item.relParams', function (newv, oldv) {
    if (newv != oldv) {
        $scope.getPreviewSQL();
    }
}, true);