$scope.getSynced = function () {
    if ($scope.model.synced) {
        return '<span style="color:green;font-size:11px;border:1px solid green;margin-left:10px;border-radius:3px;padding:1px 3px;background:white;"><i class="fa fa-check"></i> Synced</span>';
    } else {
        return '<span style="color:orange;font-size:11px;border:1px solid orange;margin-left:10px;border-radius:3px;padding:1px 3px;background:white;"><i class="fa fa-warning"></i> Please Type to Sync</span>';
    }
};
$scope.importStatus = $scope.getSynced();
$scope.saveImport = function () {
    $scope.importStatus = '<span style="font-size:11px;float:right;margin-right:110px;">Saving...</span>';
    $scope.importStatus += $scope.getSynced();

    $http.post(Yii.app.createUrl('/dev/genModule/saveImport', {active: $scope.params.active}), {
        code: $scope.model.imports
    }).success(function (synced) {
        $scope.model.synced = synced == 'true';
        $scope.importStatus = '<span style="font-size:11px;color:green;float:right;margin-right:110px;"><i class="fa fa-check"></i> Saved</span>';
        $scope.importStatus += $scope.getSynced();
    });
}

$(window).resize(function () {
    if ($(".source-editor").length > 0) {
        $(".source-editor").each(function () {
            $(this).height($(window).height() - $(this).offset().top);
        });
    }
}).resize();

$scope.setTab = function (t) {
    $storage['genModule'] = {};
    $storage['genModule'].activeTab = t;
}

if ($storage['genModule']) {
    $scope.activeTab = $storage['genModule'].activeTab == 2;
}
if ($scope.params.tab) {
    $scope.activeTab = $scope.params.tab;
}

if (!!$scope.model.name) {
    $scope.form.title = 'Module - ' + $scope.model.name;
}

$timeout(function () {
    $scope.acStatus = '';
    $scope.saveAC = function () {
        $scope.acStatus = '<span id="ac-status" style="font-size:11px;float:right;margin-left:10px;"><i class="fa fa-spin fa-refresh"></i> Saving...</span>'
        var post = {};

        if ($scope.model.accessType == 'CUSTOM') {
            post = {
                accessType: $scope.model.accessType,
                code: $scope.model.acSource
            };
        } else {
            post = {
                accessType: $scope.model.accessType,
                defaultRule: $scope.model.defaultRule,
                roles: $scope.model.rolesRule,
                users: $scope.model.usersRule,
            };
        }

        $http.post(Yii.app.createUrl('/dev/genModule/saveAc', {active: $scope.params.active}), post).success(function (data) {
            $scope.acStatus = '<span id="ac-status" style="font-size:11px;color:green;float:right;margin-left:10px;"><i class="fa fa-check"></i> Saved</span>'
            $timeout(function () {
                $("#ac-status").fadeOut(4000);
                $(window).resize();
            });

            if (!!data.acSource) {
                $scope.model.acSource = data.acSource;
            }
        });
    }
    
});