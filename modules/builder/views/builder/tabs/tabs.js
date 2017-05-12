/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $, angular */

app.controller("Tabs", function($scope, $http, $timeout, $q) {
     window.tabs = $scope;

     $scope.list = [];
     $scope.unlist = {};
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
                    if (it.size < 200) {
                         item.code.content = it.code.content;
                    }
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
                    var closed = $scope.list.splice(0, $scope.list.length);
                    closed.forEach(function(citem) {
                         window.mode[item.mode].close(citem);
                         $scope.unlist[citem.id] = citem;
                    });

                    $scope.active = null;
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Other Tabs",
               click: function(item) {
                    var closed = $scope.list.splice($scope.cm.activeIdx + 1, $scope.list.length - $scope.cm.activeIdx - 1);
                    closed.forEach(function(citem) {
                         window.mode[item.mode].close(citem);
                    });

                    if ($scope.activeIdx >= $scope.cm.activeIdx) {
                         $scope.active = $scope.list[$scope.cm.activeIdx];
                    }

                    var closed = $scope.list.splice(0, $scope.cm.activeIdx);
                    closed.forEach(function(citem) {
                         window.mode[item.mode].close(citem);
                         $scope.unlist[citem.id] = citem;
                    });

                    if ($scope.activeIdx < $scope.cm.activeIdx) {
                         $scope.active = $scope.list[0];
                    }
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Tabs to the Left",
               click: function(item) {
                    var closed = $scope.list.splice(0, $scope.cm.activeIdx);
                    closed.forEach(function(citem) {
                         window.mode[item.mode].close(citem);
                         $scope.unlist[citem.id] = citem;
                    });

                    if ($scope.activeIdx < $scope.cm.activeIdx) {
                         $scope.active = $scope.list[0];
                    }
                    $scope.updateTabHash();
                    $scope.removeClosedTab();
               }
          }, {
               label: "Close Tabs to the Right",
               click: function(item) {
                    var closed = $scope.list.splice($scope.cm.activeIdx + 1, $scope.list.length - $scope.cm.activeIdx - 1);
                    closed.forEach(function(citem) {
                         window.mode[item.mode].close(citem);
                         $scope.unlist[citem.id] = citem;
                    });

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

     $scope.editRequest = {};
     $scope.requestEdit = function(item) {
          if (item) {
               $scope.editRequest[item.id] = item;
               // this will send request edit ws to current editor
               // and then they will send their content, and lock the editor
               // then they will tell us that we can edit the file
               window.builder.ask('request-edit', item.id);
          }
     }

     $scope.itemMouseOut = function(e, item) {
          if ($(e.target).hasClass("tab-icon-x")) return;

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
          if ($(e.target).hasClass("tab-icon-x")) return;

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
          if ($(e.target).hasClass("tab-icon-x")) return;

          e.preventDefault();
          e.stopPropagation();
          $scope.drag.inititem = item;
          if (e.which == 1) {
               $scope.drag.item = item;
               $scope.drag.idx = idx;
          }
     }
     $scope.itemMouseUp = function(e, item, idx) {
          if ($(e.target).hasClass("tab-icon-x")) return;

          e.preventDefault();
          e.stopPropagation();

          if (!$scope.drag.inititem) return;
          if (item.id == $scope.drag.inititem.id) {
               switch (e.which) {
                    case 1: // this is left click
                         if ($scope.drag.item != null) {
                              $scope.drag.item = null;
                              $scope.drag.inititem = null;

                              $scope.list.forEach(function(item, idx) {
                                   var tab = $scope.stripItem(item);
                                   tab.idx = idx;
                                   window.builder.set('tabs.list.' + tab.id, tab); // reset tab.idx on open
                              });
                         }
                         break;
                    case 2: // this is middle click
                         break;
                    case 3: // this is right click
                         $scope.drag.item = null;
                         $scope.drag.inititem = null;
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

     $scope.editingTooltip = function(who) {
          if ($scope.canEdit(who)) {
               return 'You are now editing this file';
          }
     }

     $scope.canEdit = function(who) {
          if (who.editing) {
               if (who.editing.cid == window.builder.statusbar.me.cid) {
                    return true;
               }
          }
          else {
               return true;
          }
          return false;
     }

     $scope.editingIcon = function(who) {
          if (who.editing) {
               if (who.editing.cid == window.builder.statusbar.me.cid) {
                    return 'fa-pencil';
               }
               else {
                    return 'fa-ban';
               }
          }
     }

     $scope.open = function(item, options) {
          if (!item) return false;

          options = angular.extend({}, {
               reload: false,
               content: false
          }, options);

          var idx = $scope.findTab(item);
          if (!!$scope.list[idx]) {
               $scope.active = $scope.list[idx];
          }
          else {
               if ($scope.unlist[item.id]) {
                    $scope.active = $scope.unlist[item.id];
                    delete $scope.unlist[item.id];
               }
               else {
                    $scope.active = item;
               }
          }

          window.builder.ask('who-edit', $scope.active.id, function(user) {
               var me = window.builder.statusbar.me;
               user = JSON.parse(user);
               if (!user.cid) {
                    window.builder.ask('edit-by-me', $scope.active.id, function() {
                         $scope.active.editing = window.builder.statusbar.me;
                    });
               }
               else {
                    $scope.active.editing = user;
               }
          });

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

          window.mode[$scope.active.mode].open($scope.active, options);

          var tab = $scope.stripItem($scope.active);
          tab.loading = false;
          if (idx) {
               tab.idx = idx;
          }

          window.builder.set('tabs.list.' + tab.id, tab);
          window.builder.set('tabs.active', tab.id);
     }
     $scope.close = function(item, e) {
          if (e) {
               e.stopPropagation();
               e.preventDefault();
          }

          $scope.findTab(item, function(idx) {
               var unitem = $scope.list.splice(idx, 1);
               $scope.unlist[unitem.id] = unitem;

               $scope.updateTabHash();

               window.builder.ask('close-edit', item.id);
               window.mode[item.mode].close(item);
               $timeout(function() {
                    if ($scope.active && $scope.active.id == item.id) {
                         window.builder.del('tabs.active');
                         if ($scope.list[idx]) {
                              $scope.open($scope.list[idx]);
                         }
                         else if ($scope.list[idx - 1]) {
                              $scope.open($scope.list[idx - 1]);
                         } else {
                             $scope.active = null;
                         }
                    }
               });
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