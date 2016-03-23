$scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
window.$(document).keydown(function (event) {
    $scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
    if ((
        !(String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) &&
        !(event.which === 13 && (event.metaKey || event.ctrlKey))
    ) && !(event.which == 19)) return true;
    
    if (String.fromCharCode(event.which).toLowerCase() == 's') {
        $scope.status = 'Saving...';
        var data = {content: $scope.model.content, id: $scope.params.id};
        $http.post(Yii.app.createUrl('/dev/service/save'), data)
            .success(function (data) {
                $scope.status = "Saved";
            })
            .error(function (data) {
                $scope.status = "Save Failed!"
            });
    } else if (event.which === 13) {
        if ($scope.params.isRunning) {
            $scope.stop();
            $scope.changeTab('code');
        } else {
            $scope.start();
            $scope.changeTab('log');
        }
    } 

    event.preventDefault();
    return false;
});

$scope.isMonitoring = false;
$scope.instances = {};
$scope.log = "";
$scope.maxLines = 35;
$scope.selectedInstance = false;
$scope.tab = "code";
$scope.changeTab = function(tab) {
    $timeout(function() {
        var tabCode  = {
            code: 0,
            log: 1
        };
        $scope.tab = tab;
        angular.element($("#" + tab)).scope().$parent.tabs[tabCode[tab]].select();
        if (tab == 'code') {
            $scope.aceEditor.focus();
        }
    });
}
$scope.monitorCount = 0;
$scope.monitorService = function() {
    $timeout(function() {
        $http.get(Yii.app.createUrl('/dev/service/monitor&n=' + $scope.model.name))
        .success(function(data) {
            if (!!data && data.length > 0) {
                $scope.params.isRunning = true;
                $scope.instances = {};
                data.forEach(function(d) {
                    $scope.instances[d.id] = d;
                    if (!$scope.selectedInstance) {
                        $scope.selectedInstance = d.id;
                    }
                });
            } else {
                $scope.params.isRunning = false;
                $scope.selectedInstance = false;
                $scope.instances = {};
            }
            $scope.readLog();
            
            if ($scope.isMonitoring) {
                $scope.monitorService();
            }
        });
    }, ($scope.monitorCount > 10 ? 1000 : 0));
    $scope.monitorCount++;
}

$timeout(function() {
    $scope.isMonitoring = true;
    $scope.monitorService();
    
    if (!!$scope.params.log) {
        $scope.log = $scope.params.log;
    }
});

$scope.readLog = function() {
    var params = {
        n:$scope.model.name,
        id:$scope.selectedInstance,
        l:$scope.maxLines
    };
    $http.get(Yii.app.createUrl('/dev/service/readLog', params),{ transformResponse: function(data) { return data; }})
        .success(function(data) {
            $scope.log = data;
        });
}

$scope.stop = function() {
    $http.get(Yii.app.createUrl('/dev/service/stop&n=' + $scope.model.name))
    .success(function() {
        $scope.params.isRunning = false;
        $scope.selectedInstance = false;
    });
}

$scope.start = function() {
    $http.get(Yii.app.createUrl('/dev/service/start&n=' + $scope.model.name))
    .success(function() {
        $scope.params.isRunning = true;
        
        if (!$scope.isMonitoring) {
            $scope.isMonitoring = true;
            $scope.monitorCount = 0;
        }
    });
}