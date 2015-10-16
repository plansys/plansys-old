if($scope.params.href != ""){
	window.close();
	window.opener.location.href = $scope.params.href;
}