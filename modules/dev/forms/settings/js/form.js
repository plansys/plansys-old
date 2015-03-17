$scope.loading = {};
$scope.changeEnableAudit = function () {
    console.log($scope);
    if (!!$scope.model.auditEnable) {
        $scope.model.auditTrack = "delete,login,save,view";
    } else {
        $scope.model.auditTrack = null;
    }
}

$scope.checkLdap = function(){
    $scope.loading['ldap'] = true;
    $http.get(Yii.app.createUrl('/dev/settings/ldap')
    ).success(function (data) {
        if (data !== 'null') {
            if (!$scope.errors) {
                $scope.errors = {};
            }
            $scope.errors['ldapEnable'] = [data];
        } else {
            if ($scope.errors != null && typeof $scope.errors['ldapEnable'] != 'undefined') {
                delete($scope.errors['ldapEnable']);
            }
        }
        $scope.model.errors = JSON.stringify($scope.errors);
        delete $scope.loading['ldap'];
    });
}

$scope.sendEmail = function(){
    $scope.loading['email'] = true;
    $http({
       method : "POST",
       url : Yii.app.createUrl('/dev/settings/email'),
       data : $scope.model,
    }).success(function (data) {
        $scope.checkEmail();
    });
}

$scope.checkEmail = function(){
    $http.get(Yii.app.createUrl('/dev/settings/checkMail')
    ).success(function (data) {
        if (data !== 'null') {
            if (!$scope.errors) {
                $scope.errors = {};
            }
            $scope.errors['emailService'] = [data];
        } else {
            if ($scope.errors != null && typeof $scope.errors['emailService'] != 'undefined') {
                delete($scope.errors['emailService']);
            }
        }
        $scope.model.errors = JSON.stringify($scope.errors);
        delete $scope.loading['email'];
    });
}

$scope.checkDb = function () {
    $scope.loading['db'] = true;
    $http({
        method: "post",
        url: Yii.app.createUrl("/dev/settings/db"),
        data: {
            'sys': $scope.model.dbSys,
            'host': $scope.model.dbHost,
            'dbname': $scope.model.dbName,
            'username': $scope.model.dbUser,
            'password': $scope.model.dbPass
        }
    }).success(function (data) {

        if (data !== 'null') {
            if (!$scope.errors) {
                $scope.errors = {};
            }
            $scope.errors['dbSys'] = [data];
        } else {
            if ($scope.errors != null && typeof $scope.errors['dbSys'] != 'undefined') {
                delete($scope.errors['dbSys']);
            }

        }
        $scope.model.errors = JSON.stringify($scope.errors);
        delete $scope.loading['db'];
    });
}

$scope.checkNotif = function(){
    $scope.loading['notif'] = true;
    $http.get(Yii.app.createUrl("/dev/settings/notif")
    ).success(function (data) {
        if (data !== 'null') {
            if (!$scope.errors) {
                $scope.errors = {};
            }
            $scope.errors['notifEnable'] = [data];
        } else {
            if ($scope.errors != null && typeof $scope.errors['notifEnable'] != 'undefined') {
                delete($scope.errors['notifEnable']);
            }
        }
        $scope.model.errors = JSON.stringify($scope.errors);
        delete $scope.loading['notif'];
    });
}

$scope.checkRepo = function () {
    $scope.loading['repo'] = true;
    $http({
        method: "post",
        url: Yii.app.createUrl("/dev/settings/repo"),
        data: {
            'path': $scope.model.repoPath
        }
    }).success(function (data) {
        if (data !== 'null') {
            if (!$scope.errors) {
                $scope.errors = {};
            }
            $scope.errors['repoPath'] = [data];
        } else {
            if ($scope.errors != null && typeof $scope.errors['repoPath'] != 'undefined') {
                delete($scope.errors['repoPath']);
            }
        }
        $scope.model.errors = JSON.stringify($scope.errors);
        delete $scope.loading['repo'];
    });
}

$scope.validate = function(){
    $scope.checkDb();
    $scope.checkRepo();
    if($scope.model.ldapEnable == true){
        $scope.checkLdap();
    }
    if($scope.model.notifEnable == true){
        $scope.checkNotif();
    }
    if($scope.model.emailService != 'none'){
        $scope.sendEmail();
    }
}

$scope.validate();
var tempSubmit = $scope.form.submit;
$scope.form.submit = function (that) {
    $scope.formSubmitting = true;
    $scope.validate();
    
    var unwatch = $scope.$watch('loading', function () {
        if ($scope.objectSize($scope.loading) == 0) {
            $scope.formSubmitting = false;
            unwatch();

            if ($scope.objectSize($scope.errors) == 0) {
                tempSubmit(that);
            }
        }
    }, true);

    return false;
}