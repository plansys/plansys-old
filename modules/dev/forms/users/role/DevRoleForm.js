$scope.roleNameChange = function() {
    $scope.model.role_name = $scope.model.role_name.replace(/[^\w\.]/,'').toLowerCase();
}