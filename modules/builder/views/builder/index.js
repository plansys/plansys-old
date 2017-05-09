/* global $scope, $http, $timeout, $q, app, Yii */

window.mode = {};
app.controller("Index", function($scope, $http, $timeout, $q) {
    var width = $('#builder').attr('layout-width');
    if (!!width) {
        width = width + 'px';
    }

    $scope.layout = {
        col1: {
            width: width || '17%',
            minWidth: '220px'
        },
        col2: {
            width: 'auto'
        }
    };
    $scope.modelBaseClass = {} // mark as root scope
    window.builder = $scope;
    $scope.statusbar = {
        connected: false,
        people: [],
        me: {},
        peopleName: {},
        chatshow: false,
        chatpeek: false,
        chatpeekTimeout: false,
        msg: [],
        sendmsg: function(e) {
            if (e.keyCode == 13) {
                var msg = $(e.target).val();
                if (msg.trim() != "") {
                    $scope.ws.send('msg:' + msg);
                    $(e.target).val('');
                }
            }
        }
    }

    $scope.toggleChat = function() {
        $scope.statusbar.chatpeek = false;
        if ($scope.statusbar.chatshow) {
            $scope.statusbar.chatshow = false;
        }
        else {
            $scope.statusbar.chatshow = true;
        }

        if ($scope.statusbar.chatpeekTimeout) {
            $timeout.cancel($scope.statusbar.chatpeekTimeout);
            $scope.statusbar.chatpeekTimeout = false;
        }
    }

    $scope.showChat = function() {
        $scope.statusbar.chatshow = true;
        $scope.statusbar.chatpeek = false;
        if ($scope.statusbar.chatpeekTimeout) {
            $timeout.cancel($scope.statusbar.chatpeekTimeout);
            $scope.statusbar.chatpeekTimeout = false;
        }
    }

    $scope.getPeopleName = function(user) {
        var isyou = user.cid == $scope.statusbar.me.cid ? ' (You)' : '';
        if (typeof $scope.statusbar.peopleName[user.uid] == "undefined") {
            $http.get(Yii.app.createUrl('/builder/builder/getUserName&id=' + user.uid))
                .then(function(res) {
                    $scope.statusbar.peopleName[user.uid] = res.data;
                });
            $scope.statusbar.peopleName[user.uid] = "";
        }

        return $scope.statusbar.peopleName[user.uid] + ' #' + user.cid + isyou;
    }
    
    var initTabs = function() {
        $timeout(function() {
            if (!window.tabs) {
                initTabs();
                return;
            }
            
            $scope.tabs = window.tabs;
            $scope.$watch('tabs.active', function(i) {
                $scope.active = !!$scope.tabs.active;
            });
        })
    }
    initTabs();
    
    
    $scope.setp = false;
    $scope.uid = $("#builder").attr('uid')
    $scope.setStack = []
    var initWs = function() {
        $timeout(function() {
            if (!$scope.ws) {
                initWs();
                return;
            }
            
            $scope.ws.connected(function(u) {
                $scope.statusbar.connected = true;
                $scope.statusbar.me = u;
            });

            $scope.ws.disconnected(function() {
                $scope.statusbar.connected = false;
            });

            $scope.ws.receive(function(msg) {
                var msgp = msg.split(":");
                var type = msgp.shift();
                var content = msgp.join(":");
                switch (type) {
                    case "people":
                        $scope.statusbar.people = JSON.parse(content);
                        break;
                    case "msg":
                        $scope.statusbar.msg.push(JSON.parse(content));
                        if (!$scope.statusbar.chatshow) {
                            $scope.statusbar.chatshow = true;
                            $scope.statusbar.chatpeek = true;
                            $scope.statusbar.chatpeekTimeout = $timeout(function() {
                                $scope.statusbar.chatshow = false;
                                $scope.statusbar.chatpeek = false;
                                $scope.statusbar.chatpeekTimeout = false;
                            }, 5000);
                        }

                        $timeout(function() {
                            $('.status-people-msg-list').scrollTop($('.status-people-msg-list')[0].scrollHeight);
                        });
                        break;
                }
            });
            
            if ($scope.setStack.length > 0) {
                $scope.setStack.forEach(function(item) {
                    $scope.set(item.key, item.value, item.callback);
                });
            }
        }, 100);
    }
    initWs();
    
    $scope.set = function(key, value, callback) {
        if (!$scope.ws) {
            $scope.setStack.push({
                key: key,
                value: value,
                callback: callback
            });
            return;
        }
        $scope.ws.send('set:' + JSON.stringify({
            key: $scope.uid + "!" + key,
            val: value
        }));
        if (typeof callback == "function") {
            $timeout(function() {
                callback();
            });
        }
    }
    $scope.get = function(key, callback) {
        var url = Yii.app.createUrl('/builder/builder/getstate&key=' + key)
        $http.get(url).then(function(res) {
            if (typeof callback == "function") {
                callback(res.data);
            }
        });
    }

    $scope.getAll = function(key, callback, mode) {
        if (typeof mode == "string") {
            mode = "&mode=" + mode;
        }

        var url = Yii.app.createUrl('/builder/builder/getallstate&key=' + key + mode)
        $http.get(url).then(function(res) {
            if (typeof callback == "function") {
                callback(res.data);
            }
        });
    }

    $scope.del = function(key, callback) {
        var url = Yii.app.createUrl('/builder/builder/delstate&key=' + key)
        $http.get(url).then(function(res) {
            if (typeof callback == "function") {
                callback(res.data);
            }
        });
    }

    $scope.$on('ui.layout.resize', function(e, beforeContainer, afterContainer) {
        $scope.set('layout.width', beforeContainer.size)
    });
});