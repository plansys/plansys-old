$scope.selectedInstance = false;
$scope.selectedInstancePid = false;
$scope.currentTab = "code";
$scope.params.isRunning = $scope.model.runningInstances.length > 0;
$scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';

$scope.ws.connected(function(client) {
    $scope.ws.setTag($scope.model.name + ':0');
});

$scope.ws.receive(function(msg) {
    if (typeof msg === 'string') {
        msgp = msg.split(":");
        if (msgp[0] === 'stopped' && msgp.length == 2) {
            $timeout(function() {
                $scope.reloadModel(function() {
                    $scope.model.stoppedInstances.forEach(function(item) {
                        if (!!$scope.selectedInstance && item.pid == $scope.selectedInstance.pid) {
                            $scope.selectedInstance = item;
                        }
                    });
                });
            }, 500);
        }
        else if (msgp[0] === 'started' && msgp.length == 2) {
            $scope.viewRunningLog();
        }
        else {
            $scope.selectedInstance.output += msg;
            $scope.updateLogWindow();
        }
    }
    else {
        console.log(msg);
    }
})

$timeout(function() {
    if ($scope.params.isRunning) {
        $scope.viewRunningLog();
    }
});

$scope.viewRunningLog = function() {
    $scope.reloadModel(function() {
        $scope.selectedInstance = $scope.model.runningInstances[0];

        if (!$scope.selectedInstance) {
            $scope.selectedInstance = $scope.model.stoppedInstances[0];
        }
        $scope.selectedInstancePid = $scope.selectedInstance.pid;
        $scope.ws.setTag($scope.model.name + ':' + $scope.selectedInstancePid);
        $scope.changeTab('log');
    });
}

$scope.reloadModel = function(fn) {
    $http.get(Yii.app.createUrl('/dev/service/detail', {
        id: $scope.model.name
    }))
    .success(function(res) {
        for (var i in res) {
            $scope.model[i] = res[i];
        }
        $scope.updateLogWindow();
        $timeout(function() {
            $scope.params.isRunning = $scope.model.runningInstances.length > 0;
            if (typeof fn == 'function') {
                fn();
            }
        });
    })
}

$scope.updateLogWindow = function() {
    $("#logwindow").html($scope.selectedInstance.output);
    $timeout(function() {
        if ($("#logwindow").length > 0) {
            $('#logwindow').scrollTop($('#logwindow')[0].scrollHeight);
        }
    }, 100)
}

$scope.selInstanceChange = function(e) {
    $scope.selectedInstance = null;
    $scope.model.runningInstances.forEach(function(i) {
        if (i.pid == $scope.selectedInstancePid) {
            $scope.selectedInstance = i;
        }
    })

    if (!$scope.selectedInstance) {
        $scope.model.stoppedInstances.forEach(function(i) {
            if (i.pid == $scope.selectedInstancePid) {
                $scope.selectedInstance = i;
            }
        })
    }
    

    $scope.ws.setTag($scope.model.name + ':' + $scope.selectedInstancePid);
}

window.$(document).keydown(function(event) {
    $scope.status = navigator.platform.indexOf('Mac') > -1 ? 'Save: Cmd + S' : 'Save: Ctrl + S';
    if ((!(String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) &&
            !(event.which === 13 && (event.metaKey || event.ctrlKey))
        ) && !(event.which == 19)) return true;

    if (String.fromCharCode(event.which).toLowerCase() == 's') {
        $scope.status = 'Saving...';
        var data = {
            content: $scope.model.content,
            id: $scope.params.id
        };
        $http.post(Yii.app.createUrl('/dev/service/save'), data)
            .success(function(data) {
                $scope.status = "Saved";
            })
            .error(function(data) {
                $scope.status = "Save Failed!"
            });
    }
    else if (event.which === 13) {
        if ($scope.params.isRunning) {
            $scope.stop();
            $scope.changeTab('code');
        }
        else {
            $scope.start();
            $scope.changeTab('log');
        }
    }

    event.preventDefault();
    return false;
});
$scope.changeTab = function(tab) {
    $timeout(function() {
        var tabCode = {
            code: 0,
            log: 1
        };
        var tabchanged = $scope.currentTab != tab;
        
        $scope.currentTab = tab;

        if (tab == 'code') {
            $scope.aceEditor.focus();
        }

        $(".nav-tabs li").removeClass("active");
        $(".nav-tabs li").eq(tabCode[tab]).addClass("active");


        if (tab == 'log') {
            if (!$scope.selectedInstance) {
                if ($scope.model.runningInstances.length > 0) {
                    $scope.selectedInstance = $scope.model.runningInstances[0];
                    $scope.selectedInstancePid = $scope.selectedInstance.pid;
                } else if ($scope.model.stoppedInstances.length > 0) {
                    $scope.selectedInstance = $scope.model.stoppedInstances[0];
                    $scope.selectedInstancePid = $scope.selectedInstance.pid; 
                }
            }
            
            if (tabchanged) {
                $timeout(function() {
                    $("#logwindow").html("Rendering output...");
                });
                $timeout(function() {
                    $scope.updateLogWindow();
                },100);
            }
        }
    });
}

$scope.stop = function() {
    $http.get(Yii.app.createUrl('/dev/service/stop&n=' + $scope.model.name))
        .success(function() {
            $scope.params.isRunning = false;
        });
}

$scope.start = function() {
    $http.get(Yii.app.createUrl('/dev/service/start&n=' + $scope.model.name))
        .success(function(res) {
            if (res) {
                alert(res);
                return;
            }
            $scope.params.isRunning = true;
            $scope.changeTab('log');

            if (!$scope.isMonitoring) {
                $scope.isMonitoring = true;
                $scope.monitorCount = 0;
            }
        });
}