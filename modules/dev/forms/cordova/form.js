$scope.appBlur = function() {
    if (!$scope.model.package) {
        $scope.model.package = "com.example." + $scope.model.app;
    }
}