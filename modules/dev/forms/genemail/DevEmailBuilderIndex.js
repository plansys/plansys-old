$scope.status = 'Save: Ctrl + S';
window.$(document).keydown(function (event) {
    $scope.status = 'Save: Ctrl + S';
    if (!( String.fromCharCode(event.which).toLowerCase() == 's' && event.ctrlKey) && !(event.which == 19)) return true;
    $scope.status = 'Saving...';
    var data = {content: $scope.params.content, active: $scope.params.active};
    $http.post(Yii.app.createUrl('/dev/genEmail/save'), data)
        .success(function (data) {
            $scope.status = "Saved";
        })
        .error(function (data) {
            $scope.status = "Save Failed!"
        });

    event.preventDefault();
    return false;
});

