app.directive('webSocketState', function($timeout, $http) {
     return {
          require: '?ngModel',
          scope: true,
          compile: function(element, attrs, transclude) {
               if (attrs.ngModel && !attrs.ngDelay) {
                    attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
               }

               return function($scope, $el, attrs, ctrl) {
                    $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                    $scope.port = $el.find("data[name=port]:eq(0)").html().trim();
                    $scope.config = $.extend({
                         url: "ws://" + window.location.hostname + ":" + $scope.port,
                         tid: null,
                         uid: null,
                         sid: null,
                         cid: null,
                         tag: null,
                         reconnect: true,
                         debug: true,
                    }, JSON.parse($el.find("data[name=config]:eq(0)").html().trim()));
                    $scope.initTimer = null;
                    $scope.initWs = function() {
                         $scope.ws = new WebSocket($scope.config.url);
                         $scope.ws.onopen = function() {
                              var c = $scope.config;
                              var str = 'connect:' + c.tid + ':' + c.uid + ':' + c.sid;
                              this.send(str);
                         }
                         $scope.ws.onmessage = function(e) {
                              if (e.data.indexOf('connect:') === 0) {
                                   $timeout(function() {
                                        $scope.config.cid = e.data.split(":")[1];
                                        $scope.sendQueue.forEach(function(item) {
                                             $scope.sendQueue.shift();
                                             $scope.executeSend(item.params, item.fn);
                                        });
                                        
                                        if (typeof $scope.connectedFunc == 'function') {
                                             $timeout(function(){
                                                  $scope.connectedFunc($scope.config);
                                             });
                                        }
                                   });
                              }
                              else {
                                   if (typeof $scope.receiveFunc == 'function') {
                                        var data;
                                        if (e.data[0] == '{' || e.data[0] == '[') {
                                             try {
                                                  data = JSON.parse(e.data)
                                             } catch (exc) {
                                                  data = e.data;
                                             }
                                        } else {
                                             data = e.data;
                                        }
                                        
                                        $timeout(function(){
                                             $scope.receiveFunc(data);
                                        });
                                   }
                              }
                         }
                         $scope.ws.onerror = function(e) {
                              if (typeof $scope.disconnectedFunc == 'function') {
                                   $timeout(function(){
                                        $scope.disconnectedFunc($scope.config);
                                   });
                              }
                              
                              if (!$scope.initTimer) {
                                   $scope.initTimer = $timeout(function() {
                                        $scope.initWs();
                                        $scope.initTimer=null;
                                   }, 1000);
                              }
                         }
                         $scope.ws.onclose = function(e) {
                              if (typeof $scope.disconnectedFunc == 'function') {
                                   $timeout(function(){
                                        $scope.disconnectedFunc($scope.config);
                                   });
                              }
                              if (!$scope.initTimer) {
                                   $scope.initTimer = $timeout(function() {
                                        $scope.initWs();
                                        $scope.initTimer=null;
                                   }, 1000);
                              }
                         }
                    }
                    $scope.initWs();

                    $scope.sendQueue = [];

                    $scope.send = function(params, fn) {
                         if ($scope.config.cid) {
                              $scope.sendQueue.push({
                                   params: params,
                                   fn: fn
                              });
                              $scope.sendQueue.forEach(function(item) {
                                   $scope.sendQueue.shift();
                                   $scope.executeSend(item.params, item.fn);
                              });
                         }
                         else {
                              $scope.sendQueue.push({
                                   params: params,
                                   fn: fn
                              });
                         }
                    }

                    $scope.receiveFunc = false;
                    $scope.receive = function(fn) {
                         $scope.receiveFunc = fn;
                    }
                    
                    $scope.connectedFunc = false;
                    $scope.connected = function (fn) {
                         $scope.connectedFunc = fn;
                    };
                    
                    $scope.disconnectedFunc = false;
                    $scope.disconnected = function (fn) {
                         $scope.disconnectedFunc = fn;
                    };
                    
                    $scope.executeSend = function(params, fn) {
                         $http.post(Yii.app.createUrl('/sys/ws/send', {
                              tid: $scope.config.tid,
                              cid: $scope.config.cid
                         }), params)
                    }

                    $scope.setTag = function(tag) {
                         $http.get(Yii.app.createUrl('/sys/ws/stag',  {
                              tid: $scope.config.tid,
                              uid: $scope.config.uid,
                              sid: $scope.config.sid,
                              cid: $scope.config.cid,
                              tag: tag
                         }))
                    }

                    var parent = $scope.getParent($scope);
                    parent[$scope.name] = $scope;
               }
          }
     }
});