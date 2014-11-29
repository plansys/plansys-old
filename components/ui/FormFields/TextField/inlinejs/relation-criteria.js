
$scope.$watch('params', function (newv, oldv) {
    if (newv != oldv) {
        $scope.getPreviewSQL();
    }
}, true);
$scope.getPreviewSQL()