$scope.daemonChange = function() {
    if ($scope.model.status == "Service Daemon Stopped") {
        $http.get(Yii.app.createUrl('/dev/service/stopDaemon'));
    } else {
        $http.get(Yii.app.createUrl('/dev/service/startDaemon'));
    }
}
