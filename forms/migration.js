$scope.toggleMigration = function () {
    $scope.migration = !$scope.migration;
    $timeout(function () {
        $("#name").focus();
    }, 0);
}