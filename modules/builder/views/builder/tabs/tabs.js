/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, angular */

app.controller("Tabs", function($scope, $http, $timeout, $q) {
     window.tabs = $scope;

     $scope.list = [];
     $scope.active = null;
     $scope.activeIdx = -1;
     $scope.init = true;

     $timeout(function() {
          $scope.builder = window.builder; // bind builder so html can read it
     })

     window.builder.getAll('tabs.list.*', function(val) {
          if ($scope.isArray(val)) {
               $scope.list = val;

               window.builder.get('tabs.active', function(val) {
                    $scope.list.forEach(function(item) {
                         if (item.id == val) {
                              console.log(item);
                              $scope.open(item);
                         }
                    });
               });
          }
     }, 'tabs');
     $scope.stripItem = function(it) {
          var exclude = ['parent', 'code']
          var item = {};
          for (var i in it) {
               if (exclude.indexOf(i) < 0 && i[0] != '$') {
                    item[i] = it[i];
               }
          }
          
          if (it.code) {
               item.code = {}
               if (it.code.cursor) {
                    item.code.cursor = it.code.cursor;
               }
               if (it.unsaved && it.code.content) {
                    item.code.content = item.code.content;
               }
               if (it.code.status) {
                    item.code.status = it.code.status;
               }
          }
          
          return item;
     }

     $scope.drag = {
          idx: false,
          item: null,
     };
     $scope.cm = {
          active: null,
          hidden: false,
          activeIdx: -1,
          pos: {
               x: 0,
               y: 0
          },
          click: function(e, cb) {
               e.preventDefault();
               e.stopPropagation();
               $timeout(function() {
                    $scope.cm.hidden = true;
                    cb($scope.cm.active);
                    $timeout(function() {
                         $scope.cm.hidden = false;
                         $scope.cm.active = null;
                         $scope.cm.activeIdx = -1;
                    });
               });
          },
          menu: [{
               label: "Select in tree",
               click: function(item) {
                    $scope.selectInTree(item);
               }
          }, {
               hr: true
          }, {
               label: "Close All Tabs",
               click: function(item) {
                    $scope.list.splice(0, $scope.list.length);
                    $scope.active = null;
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Other Tabs",
               click: function(item) {
                    $scope.list.splice($scope.cm.activeIdx + 1, $scope.list.length - $scope.cm.activeIdx - 1);
                    if ($scope.activeIdx >= $scope.cm.activeIdx) {
                         $scope.active = $scope.list[$scope.cm.activeIdx];
                    }

                    $scope.list.splice(0, $scope.cm.activeIdx);
                    if ($scope.activeIdx < $scope.cm.activeIdx) {
                         $scope.active = $scope.list[0];
                    }
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Tabs to the Left",
               click: function(item) {
                    $scope.list.splice(0, $scope.cm.activeIdx);
                    if ($scope.activeIdx < $scope.cm.activeIdx) {
                         $scope.active = $scope.list[0];
                    }
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Tabs to the Right",
               click: function(item) {
                    $scope.list.splice($scope.cm.activeIdx + 1, $scope.list.length - $scope.cm.activeIdx - 1);
                    if ($scope.activeIdx >= $scope.cm.activeIdx) {
                         $scope.active = $scope.list[$scope.cm.activeIdx];
                    }
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }]
     }
     $(window).mouseup(function(e) {
          $scope.drag.inititem = null;
          if ($scope.drag.item) {
               $scope.drag.item = null;
          }
     });
     $(document).keydown(function(event) {
          if (!(String.fromCharCode(event.which).toLowerCase() == 's' &&
                    (event.metaKey || event.ctrlKey)) &&
               !(event.which == 19)) return true;

          $scope.save();
          event.preventDefault();
          return false;
     });
     $scope.save = function() {
          window.mode[$scope.active.mode].save();
     }
     $scope.getUrl = function(item) {
          if (!item) return "";
          return Yii.app.createUrl('builder/code&f=' + item.d);
     }
     $scope.itemMouseOut = function(e, item) {
          e.preventDefault();
          e.stopPropagation();
          if (!$scope.drag.item) return;
          if ($scope.drag.item && item.id != $scope.drag.item && !$scope.drag.el) {
               if ($scope.drag.touchTimeout) {
                    $timeout.cancel($scope.drag.touchTimeout);
               }

               var el = e.target;
               if (!$(e.target).hasClass('tab')) {
                    el = $(e.target).parents('.tab');
               }
          }
     }
     $scope.itemMouseOver = function(e, item, idx) {
          e.preventDefault();
          e.stopPropagation();
          if ($scope.drag.item) {
               if (e.offsetX > $(e.target).width() / 4) {
                    $scope.list.splice($scope.drag.idx, 1);
                    $scope.list.splice(idx, 0, $scope.drag.item);
                    $scope.drag.idx = idx;
                    $scope.activeIdx = idx;
                    $scope.updateTabHash();
               }
          }
     }
     $scope.itemMouseDown = function(e, item, idx) {
          e.preventDefault();
          e.stopPropagation();
          $scope.drag.inititem = item;
          if (e.which == 1) {
               $scope.drag.item = item;
               $scope.drag.idx = idx;
          }
     }
     $scope.itemMouseUp = function(e, item, idx) {
          e.preventDefault();
          e.stopPropagation();

          if (!$scope.drag.inititem) return;

          if (item.id == $scope.drag.inititem.id) {
               $scope.drag.item = null;
               $scope.drag.inititem = null;
               switch (e.which) {
                    case 1: // this is left click
                         break;
                    case 2: // this is middle click
                         break;
                    case 3: // this is right click
                         $scope.showContextMenu(item, idx, e.clientX, e.clientY);
                         break;
                    default:
                         alert("you have a strange mouse!");
                         break;
               }
          }
     }

     $scope.removeClosedTab = function() {
          $timeout(function() {
               var url = Yii.app.createUrl('/builder/builder/removeClosedTab');
               var list = [];
               $scope.list.forEach(function(item) {
                    list.push(item.id);
               })
               $http.post(url, list);
          });
     }

     $scope.showContextMenu = function(item, idx, x, y) {
          $scope.cm.active = item;
          $scope.cm.activeIdx = idx;
          $scope.cm.pos.x = x;
          $scope.cm.pos.y = y;

          $timeout(function() {
               var h = $(".context-menu .dropdown-menu").height();
               var wh = $(window).height()
               if (y + h > wh) {
                    y = wh - h - 10;
               }

               var w = $(".context-menu .dropdown-menu").width();
               var ww = $(window).width()
               if (x + w > ww) {
                    x = ww - w - 10;
               }
               $scope.cm.pos.x = x;
               $scope.cm.pos.y = y;
          });
     }

     $scope.selectInTree = function(item) {
          window.tree.expandToItem(item, function() {
               window.tree.select(item);
               $timeout(function() {
                    if ($(window).width() < 768) {
                         $scope.active = null;
                    }
               });
          });
     }

     $scope.open = function(item) {
          if (!item) return false;

          var idx = $scope.findTab(item);
          if (!!$scope.list[idx]) {
               $scope.active = $scope.list[idx];
          }
          else {
               $scope.active = item;
          }

          if (idx === false) {
               $scope.active.loading = true;
               $scope.activeIdx = $scope.list.length;
               $scope.list.push(item);

               $timeout(function() {
                    var cleanItem = angular.copy(item);
                    cleanItem.loading = false;
                    if (cleanItem.code) {
                         delete cleanItem.code;
                    }
                    $scope.updateTabHash();
               });
          }
          else {
               $scope.activeIdx = idx;
          }

          if (!$scope.active.mode) {
               $scope.active.mode = 'code';
               if (['jpg', 'png', 'gif', 'jpeg'].indexOf($scope.active.ext.toLowerCase()) >= 0) {
                    $scope.active.mode = 'image';
               }
          }
          window.mode[$scope.active.mode].open($scope.active);

          var tab = $scope.stripItem($scope.active);
          tab.loading = false;
          if (idx) {
               tab.idx = idx;
          }
          console.log(tab);
          window.builder.set('tabs.list.' + tab.id, tab); // reset tab.idx on open
          window.builder.set('tabs.active', tab.id);
     }
     $scope.close = function(item, e) {
          if (e) {
               e.stopPropagation();
               e.preventDefault();
          }

          $scope.findTab(item, function(idx) {
               $scope.list.splice(idx, 1);
               $scope.updateTabHash();

               if ($scope.active && $scope.active.id == item.id) {
                    window.builder.del('tabs.active');
                    if ($scope.list[idx]) {
                         $scope.open($scope.list[idx]);
                    }
                    else if ($scope.list[idx - 1]) {
                         $scope.open($scope.list[idx - 1]);
                    }
                    else {
                         $scope.active = null;
                         window.mode.code.close();
                    }
               }
          });

          $scope.removeClosedTab();
     }

     $scope.findTab = function(item, success) {
          for (var i in $scope.list) {
               if ($scope.list[i].id == item.id) {
                    if (success) {
                         success(i);
                    }

                    return i;
               }
          }
          return false;
     }

     var uthTimer = false;
     $scope.updateTabHash = function() {
          var hash = {};
          var hidx = {}
          $scope.list.forEach(function(item, idx) {
               hash[idx] = item.d;
               hidx[item.id] = idx;
          });

          if (uthTimer) {
               $timeout.cancel(uthTimer);
          }

          uthTimer = $timeout(function() {
               var url = Yii.app.createUrl('/builder/builder/updateTabIndex');
               $http.post(url, hidx);
               uthTimer = false;
          }, 400);
     }
});

app.directive('selectOnClick', ['$window', function($window) {
     return {
          restrict: 'A',
          link: function(scope, element, attrs) {
               element.on('click', function() {
                    if (!$window.getSelection().toString()) {
                         // Required for mobile Safari
                         this.setSelectionRange(0, this.value.length)
                    }
               });
          }
     };
}]);