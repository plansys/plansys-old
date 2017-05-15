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


    if ($("#chat-data").text().trim() != "") {
        $scope.statusbar.msg = JSON.parse($("#chat-data").text());
    }

    window.addEventListener("beforeunload", function(e) {
        var confirmationMessage = 'It looks like you have been editing something. ' +
            'If you leave before saving, your changes will be lost.';

        for (tab in window.tabs.list) {
            if (window.tabs.list[tab].unsaved || window.tabs.list[tab].loading) {
                (e || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
            }
        }
    });

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

    $scope.getPeopleName = function(user, simple) {
        if (!user || !user.uid) return "";

        var isyou = user.cid == $scope.statusbar.me.cid ? ' (You)' : '';
        if (typeof $scope.statusbar.peopleName[user.uid] == "undefined") {
            $http.get(Yii.app.createUrl('/builder/builder/getUserName&id=' + user.uid))
                .then(function(res) {
                    $scope.statusbar.peopleName[user.uid] = res.data;
                });
            $scope.statusbar.peopleName[user.uid] = "";
        }

        if (simple) {
            if (isyou) return 'You';
            else {
                return $scope.statusbar.peopleName[user.uid] + ' #' + user.cid
            }
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
    $scope.setStack = [];
    $scope.askStack = {};
    var initWs = function() {
        $timeout(function() {
            if (!$scope.ws) {
                initWs();
                return;
            }

            $scope.ws.connected(function(u) {
                $scope.statusbar.connected = true;
                $scope.statusbar.me = u;
                console.log("WS Connected")
            });

            $scope.ws.disconnected(function() {
                $scope.statusbar.connected = false;
                console.log("WS Disonnected")

            });

            $scope.ws.receive(function(msg) {
                var msgp = msg.split(":");
                var type = msgp.shift();
                var content = msgp.join(":");
                switch (type) {
                    case "ask":
                        var askp = content.split("~")
                        var ask = askp.shift();
                        var answer = askp.join("~");
                        if ($scope.askStack[ask]) {
                            $scope.askStack[ask](answer);
                            delete($scope.askStack[ask]);
                        }
                        break;
                    case "request-edit":
                        var itemp = content.split("|");
                        var ed = itemp.shift();
                        var from = JSON.parse(itemp.join("|"));
                        window.tabs.list.forEach(function(item) {
                            if (item.id == ed) {
                                item.editing = from;
                                window.builder.ask('you-can-edit', JSON.stringify({
                                    itemid: item.id,
                                    editor: from,
                                    code: item.code.content,
                                    unsaved: item.unsaved
                                }));
                            }
                        });
                        break;
                    case "you-can-edit":
                        var e = JSON.parse(content);
                        window.tabs.list.forEach(function(item) {
                            if (item.id == e.itemid) {
                                item.editing = $scope.statusbar.me;
                                if (!item.code) {
                                    item.code = {};
                                }
                                if (window.tabs.editRequest[item.id]) {
                                    delete window.tabs.editRequest[item.id];
                                }
                                item.unsaved = e.unsaved;
                                window.tabs.open(item, {
                                    reload: true,
                                    content: e.code
                                });
                            }
                        });
                        break;
                    case "people":
                        $scope.statusbar.people = JSON.parse(content);

                        if (window.tabs.active && window.tabs.active.editing && $scope.statusbar.me.cid) {
                            var active = window.tabs.active.editing;

                            if (active.cid != $scope.statusbar.me.cid) { // kalau yg ngedit skrg bukan aku (aku g bisa ngedit)
                                var found = false;
                                $scope.statusbar.people.forEach(function(u) {
                                    if (u.cid == active.cid) { // kalau yg ngedit skrg masih online
                                        // cari tahu siapa yg ngedit file ini selain saya
                                        window.builder.ask('who-edit', $scope.active.id, function(user) {
                                            // kalau ga ada, ya biar saya aku aja yg edit
                                            if (!user.cid) {
                                                window.builder.ask('edit-by-me', $scope.active.id, function() {
                                                    $scope.active.editing = window.builder.statusbar.me;
                                                    window.tabs.open(window.tabs.active);
                                                });
                                            }
                                        });
                                        found = true;
                                    }
                                });
                                if (!found) {
                                    window.tabs.open(window.tabs.active);
                                }
                            }
                        }
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

    $scope.wsready = function(f) {
        if (!$scope.ws) {
            $timeout(function() {
                $scope.wsready(f);
            }, 500)
        }
        else {
            f();
        }
    }

    $scope.set = function(key, value, callback) {
        $scope.wsready(function() {
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
        });
    }

    $scope.ask = function(question, content, callback) {
        $scope.wsready(function() {
            content = content + "";
            question = question.replace(/\|/g, '.');
            content = content.replace(/\|/g, '.');
            $scope.askStack[question + "|" + content] = callback;
            $scope.ws.send('ask:' + question + "|" + content);
        });
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