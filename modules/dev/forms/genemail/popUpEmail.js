if ($scope.params.href != "") {
    window.close();
    window.opener.location.href = $scope.params.href;
}

if (!!window.opener.activeItem) {
    if ($scope.model == null) {
        $scope.model = {};
    }
    
    $scope.model.module = window.opener.activeItem.type;
}