$scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
window.$(document).keydown(function (event) {
    $scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
    if (!(String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19))
        return true;
    $scope.status = 'Saving...';
    var data = {content: $scope.params.content, active: $scope.params.active};

    $http.post(Yii.app.createUrl('/dev/genModel/save'), data)
            .success(function (data) {
                $scope.status = "Saved";
                $timeout(function () {
                    $scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
                }, 1000);
            })
            .error(function (data) {
                $scope.status = "Save Failed!"
            });

    event.preventDefault();
    return false;
});

if (!!$scope.params.name) {
    $scope.form.title = 'Model - ' + $scope.params.name;
}

$scope.isChanged = true;

$scope.select = function () {
    console.log('asdas');
}

$scope.markUnchange = function() {
    $scope.isChanged = false;
}

$scope.tabSelect = function() {
    $scope.isChanged = true;
    location.hash = '';
}

$scope.tabRulesSelect = function() {
    location.hash = '#rules';
}

$scope.tabRelSelect = function() {
    location.hash = '#rel';
    var scope = angular.element($('[name="DevGenModelRelations[dsRel]"]')[0]).scope(); 
    if (scope) {
        scope.query();
        $scope.markUnchange();
    } 
}
