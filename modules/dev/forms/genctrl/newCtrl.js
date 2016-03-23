function formatCtrlName(result) {
    result = result.replace(/ /g, '');
    return result.charAt(0).toUpperCase() + result.slice(1);
}

$scope.nameChange = function () {
    $scope.model.ctrlName = formatCtrlName($scope.model.ctrlName);
}

$scope.model.module = window.opener.activeItem.module;
if (window.opener.activeItem.label.toLowerCase() != window.opener.activeItem.module) {
    $scope.model.module += "." + window.opener.activeItem.label.toLowerCase();
}

$timeout(function () {
    $("#DevGenNewCtrl_5").focus();
}, 100);

if ($scope.params.href != "") {
    window.close();
    window.opener.location.href = $scope.params.href;
}