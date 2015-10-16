$scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
window.$(document).keydown(function (event) {
    $scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
    if (!( String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19)) return true;
    $scope.status = 'Saving...';
    var data = {content: $scope.params.content, active: $scope.params.active};
    $http.post(Yii.app.createUrl('/dev/genCtrl/save'), data)
        .success(function (data) {
            $scope.status = "Saved";
        })
        .error(function (data) {
            $scope.status = "Save Failed!"
        });

    event.preventDefault();
    return false;
});

if (!!$scope.params.name) {
    $scope.form.title = 'Ctrl - ' + $scope.params.name;
}