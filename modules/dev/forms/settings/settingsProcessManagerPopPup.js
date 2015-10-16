if($scope.params.href != ""){
	window.close();
	window.opener.location.href = $scope.params.href;
}


$scope.processUrlChange = function() {
    $scope.prefix = $scope.model.processUrl.split(".").pop().replace(/Command$/, "").toLowerCase();
    $scope.model.processName = $scope.prefix;
} 