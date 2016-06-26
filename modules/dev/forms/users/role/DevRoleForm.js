if ($scope.model.role_name) {
    $scope.model.module = $scope.model.role_name.split(".").shift();
}

$scope.roleNameChange = function() {
    var rn = '';
    
    if ($scope.model.role_name) {
       rn = $scope.model.role_name.split(".");
    }
    if (rn.length > 1) {
        rn = rn.splice(1);
        rn = rn.join(".");
    } else {
        if ($scope.model.module == rn) {
            $scope.model.role_name = $scope.model.module + ".";
            if ($scope.model.role_name.trim() == '.') {
                $scope.model.role_name = "";
            }
            return "";
        }
    }
    
    var md = !!$scope.model.module ? $scope.model.module + "." : "";
    if (!rn.replace) {
        rn = rn + "";
    }
    $scope.model.role_name = md + rn.replace(/[^\w]/,'').toLowerCase();
   
}
