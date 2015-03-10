$scope.importStatus = '';
$scope.saveImport = function () {

    $scope.importStatus = '<span style="font-size:11px;float:right;margin-right:110px;">Saving...</span>';
    $http.post(Yii.app.createUrl('/dev/genModule/saveImport', {active: $scope.params.active}), {
        code: $scope.model.imports
    }).success(function () {
        $scope.importStatus = '<span style="font-size:11px;color:green;float:right;margin-right:110px;"><i class="fa fa-check"></i> Saved</span>';
        ;
    });
}

$(window).resize(function () {
    if ($("#import-editor").length > 0) {
        $("#import-editor").height($(window).height() - $(".section-header").offset().top - 28);
    }
}).resize();

$scope.setTab = function (t) {
    $storage['genModule'] = {};
    $storage['genModule'].activeTab = t;
}

if ($storage['genModule']) {
    $scope.activeTab = $storage['genModule'].activeTab == 2;
}

$scope.acStatus = '';
$scope.saveAC = function () {
    $scope.acStatus = '<span id="ac-status" style="font-size:11px;float:right;margin-left:10px;"><i class="fa fa-spin fa-refresh"></i> Saving...</span>'

    $http.post(Yii.app.createUrl('/dev/genModule/saveAc', {active: $scope.params.active}), {
        defaultRule: $scope.model.defaultRule,
        accessType: $scope.model.accessType,
        roles: $scope.roleAccessDs.data,
        users: $scope.userAccessDs.data
    }).success(function (data) {
        $scope.acStatus = '<span id="ac-status" style="font-size:11px;color:green;float:right;margin-left:10px;"><i class="fa fa-check"></i> Saved</span>'
        $timeout(function () {
            $("#ac-status").fadeOut(4000);
        })
    });
}

$scope.roleAccess.afterCellEdit = function () {
    $scope.saveAC();
}
$scope.userAccess.afterCellEdit = function () {
    $scope.saveAC();
}