$scope.model.module = $scope.model.role_name.split(".").shift();

$scope.roleNameChange = function() {
    var rn = $scope.model.role_name.split(".");
    if (rn.length > 1) {
        rn = rn.splice(1);
    } else {
        if ($scope.model.module == rn) {
            $scope.model.role_name = $scope.model.module + ".";
            return "";
        }
    }
    rn = rn.join(".");
    var md = !!$scope.model.module ? $scope.model.module + "." : "";
    
    $scope.model.role_name = md + rn.replace(/[^\w]/,'').toLowerCase();
}
