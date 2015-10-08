$scope.params.status = 'Save: Ctrl + S';
window.$(document).keydown(function (event) {
    $scope.params.status = 'Save: Ctrl + S';
    if (!( String.fromCharCode(event.which).toLowerCase() == 's' && event.ctrlKey) && !(event.which == 19)) return true;
    $scope.params.status = 'Saving...';

    var data = {
        content: $scope.params.content,
        name: $scope.params.name,
        ext: $scope.params.ext
    };
    $http.post(Yii.app.createUrl('/dev/forms/codeSave'), data)
        .success(function (data) {
            $scope.params.status = "Saved";
        })
        .error(function (data) {
            $scope.params.status = "Save Failed!"
        });

    event.preventDefault();
    return false;
});