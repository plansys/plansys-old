/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, angular */

app.controller("Tabs", function($scope, $http, $timeout, $q) {
     window.tabs = $scope;

     $scope.list = [];
     $scope.active = null;
     $scope.activeIdx = -1;
     $scope.init = true;
     // var store = window.localStorage;

     $timeout(function() {
          $scope.builder = window.builder;
          $scope.tree = window.tree;
          $scope.code = window.code;
     });

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
               }
          }, {
               label: "Close Tabs to the Left",
               click: function(item) {
                    $scope.list.splice(0, $scope.cm.activeIdx);
                    if ($scope.activeIdx < $scope.cm.activeIdx) {
                         $scope.active = $scope.list[0];
                    }
                    $scope.updateTabHash();
               }
          }, {
               label: "Close Tabs to the Right",
               click: function(item) {
                    $scope.list.splice($scope.cm.activeIdx + 1, $scope.list.length - $scope.cm.activeIdx - 1);
                    if ($scope.activeIdx >= $scope.cm.activeIdx) {
                         $scope.active = $scope.list[$scope.cm.activeIdx];
                    }
                    $scope.updateTabHash();
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
          if ($scope.active.mode == 'code') {
               window.code.save();
          }
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
          $scope.tree.expandToItem(item, function() {
               $scope.tree.select(item);
               $timeout(function() {
                   if ($(window).width() < 768) {
                       $scope.active = null;
                   }
               });
          });
     }
     $scope.open = function(item) {
          if (!item) return false;

          $scope.active = item;
          var idx = $scope.findTab(item);
          if (idx === false) {
               $scope.activeIdx = $scope.list.length;
               $scope.list.push(item);

               $timeout(function() {
                    var cleanItem = angular.copy(item);
                    cleanItem.loading = false;
                    if (cleanItem.code) {
                         delete cleanItem.code;
                    }
                    // store['tabs-' + item.d] = JSON.stringify(cleanItem);
                    $scope.updateTabHash();
               });
          }
          else {
               $scope.activeIdx = idx;
          }
          // store['tabs-active'] = $scope.activeIdx;

          // by default open code editor
          if (!item.mode || item.mode == 'code') {
               item.mode = 'code';
               if (!item.code || !item.code.session) {
                    item.loading = true;
                    $http({
                         url: Yii.app.createUrl('/builder/code&f=' + item.d),
                         method: 'GET',
                         transformResponse: undefined
                    }).then(function(res) {
                         if (!!item.code && item.code.content && !$scope.init) {
                              var newcontent = item.code.content;
                              $scope.init = true;
                         }
                         item.code = {
                              content: res.data
                         };
                         item.loading = false;
                         if ($scope.active.id == item.id) {
                              if (newcontent) {
                                   window.code.open(item, newcontent);
                              }
                              else {
                                   window.code.open(item);
                              }
                         }
                    });
               }
               else {
                    window.code.open(item);
               }
          }

     }
     $scope.close = function(item, e) {
          if (e) {
               e.stopPropagation();
               e.preventDefault();
          }
          if (item.unsaved) {
               if (!confirm("This file has not been saved!\nAre you sure want to close this file ?")) {
                    return true;
               }
          }

          $scope.findTab(item, function(idx) {
               $scope.list.splice(idx, 1);
               $scope.updateTabHash();
               // if (store['tabs-' + item.d]) {
               //      delete store['tabs-' + item.d];
               // }

               if ($scope.active && $scope.active.id == item.id) {
                    if ($scope.list[idx]) {
                         $scope.open($scope.list[idx]);
                    }
                    else if ($scope.list[idx - 1]) {
                         $scope.open($scope.list[idx - 1]);
                    }
                    else {
                         $scope.active = null;
                         // store['tabs-active'] = -1;
                    }
               }
          });

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
     $scope.updateTabHash = function() {
          var hash = {};
          $scope.list.forEach(function(item, idx) {
               hash[idx] = item.d;
          });
          // store['tabs-hash'] = JSON.stringify(hash);
     }

     // $timeout(function() { // first load 
     //      var store = window.localStorage;
     //      if (store['tabs-hash']) {
     //           var tabs = JSON.parse(store['tabs-hash']);
     //           for (var i in tabs) {
     //                var item = JSON.parse(store['tabs-' + tabs[i]]);

     //                if (store['tabs|code-' + tabs[i]]) {
     //                     item.unsaved = true;
     //                     item.code = JSON.parse(store['tabs|code-' + tabs[i]]);
     //                }
     //                if (store['tabs-' + tabs[i]]) {
     //                     $scope.list.push(item);
     //                }
     //           }

     //           $scope.open($scope.list[store['tabs-active']]);
     //      }
     // });
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