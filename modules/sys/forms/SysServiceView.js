/* global $scope, $timeout, $http, Yii */
$scope.poolFailedCount = 0;
$scope.maxPoolFailed = 5;
$scope.poolDots = ".";
$scope.poolCount = function() {
    $scope.poolFailedCount++;
    
    if ($scope.poolDots.length < 3) {
        $scope.poolDots += ".";
    } else {
        $scope.poolDots = ".";
    }
    
    if ($scope.poolFailedCount >= $scope.maxPoolFailed) {
        location.href = Yii.app.createUrl('/sys/service/instanceNotFound', {full: $scope.params.full || 0});
    }
}
$scope.pool = function() {
    $timeout(function() {
        $http.get(Yii.app.createUrl('/sys/service/pool',{
                name: $scope.params.name,
                id: $scope.params.id
            })
        ).success(function(data) {
            $scope.params.svc = data.svc;
            if ($scope.params.svc == "finished" || 
                (!!$scope.params.svc && !!$scope.params.svc.view && !!$scope.params.svc.view.failed)) {
                return true;
            } 
            if (!!$scope.params.svc && !!$scope.params.svc.view && !!$scope.params.svc.view.redirect) {
                location.href = Yii.app.createUrl($scope.params.svc.view.redirect);
                return true;
            }
            
            $scope.pool();
            if ($scope.params.svc == null) {
                $scope.poolCount();
            }
        }).error(function() {
            $scope.pool();
            $scope.poolCount();
        });
    }, 1000);
}

$scope.pool();