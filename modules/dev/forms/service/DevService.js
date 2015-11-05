if($scope.params.href != ""){
	window.close();
	window.opener.location.href = $scope.params.href;
}

$scope.listCommand = false;
$scope.listAction = false;

$scope.formatClass = function(str) {
    return ucfirst(str.replace(/\W/g, ''));
}

$scope.onModuleChange = function() {
    $http.get(Yii.app.createUrl('/dev/service/listCommand', {m: $scope.model.commandPath}))
    .success(function(result) {
        if (!!result) {
            $scope.$newCommand = false;
            $scope.listCommand = result;
            $scope.model.command = '';
            $scope.model.action = '';
        } else {
            $scope.$newCommand = true;
            $scope.listCommand = false;
            $scope.listAction = false;
        }
    });
}

$scope.onCommandChange = function() {
    $http.get(Yii.app.createUrl('/dev/service/listAction', {
        m: $scope.model.commandPath, 
        c: $scope.model.command,
        n: $scope.params.isNewRecord ? "y" : "t"
    }))
    .success(function(result) {
        if (!!result) {
            $scope.listAction = result;
            $scope.$newCommand = false;
        } else {
            $scope.$newCommand = true;
            $scope.listCommand = false;
            $scope.listAction = false;
        }
    });
}

if (!$scope.params.isNewRecord) {
    $scope.onCommandChange();
}
