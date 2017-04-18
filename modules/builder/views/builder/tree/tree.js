/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $ */

app.controller("Tree", function($scope, $http, $timeout, $q) {
     window.tree = $scope;
     $scope.selected = null;
     $scope.tree = [];

     $timeout(function() {
          $scope.tabs = window.tabs;
          $scope.builder = window.builder;
     });

     $scope.search = {
          loading: false,
          paging: {
               init: 15,
               next: 30
          },
          text: "",
          detail: {
               show: false,
               path: '/app'
          },
          tree: [],
          rawtree: []
     };
     $scope.drag = {
          item: null
     };
     $scope.cm = {
          active: null,
          pos: {
               x: 0,
               y: 0
          },
          click: function(e, cb, menu) {
               e.preventDefault();
               e.stopPropagation();
               var cm = $scope.cm.active;
               $scope.cm.active = null;
               $timeout(function() {
                    cb(cm, menu);
               });
          },
          getLabel: function(menu) {
               if (typeof menu.label == 'function') {
                    return menu.label($scope.cm.active)
               }
               else {
                    return menu.label
               }
          },
          menu: [{
               icon: "fa fa-fw fa-file",
               visible: function(item) {
                    var specialDirs = ['websockets', 'forms', 'modules', 'commands', 'controllers', 'model'];
                    var name = item.n;
                    if (item.t != 'dir') {
                         name = item.parent.n;
                    } 
                    
                    if (specialDirs.indexOf(name) >= 0) {
                         return true;
                    }
                    return false;
               },
               label: function(item) {
                    var name = item.n;
                    if (item.t != 'dir') {
                         name = item.parent.n;
                    } 
                    switch (name) {
                         case "websockets":
                              return "New Ws Controller";
                         case "controllers":
                              return "New Controller";
                         case "commands":
                              return "New Service Command";
                         case "forms":
                              return "New Form";
                         case "models":
                              return "New Model";
                    }
               },
               click: function(item, menu) {
                    $timeout(function() {
                         var name = prompt(menu.label(item) + " name:");
                         if (!name) return;
                         
                         $http.get(Yii.app.createUrl('/builder/tree/touch', {
                              path: item.p,
                              name: name,
                              mode: item.n
                         })).then(function(res) {
                              if (item.childs) {
                                   item.childs.splice(0, item.childs.length);
                              }
                              $scope.expand(item, function() {
                                   for (var i in item.childs) {
                                        if (item.childs[i].p == res.data) {
                                             $timeout(function() {
                                                  $scope.select(item.childs[i]);
                                                  $scope.open(item.childs[i]);
                                             });
                                             break;
                                        }
                                   }
                              });
                         });
                    });
               }
          }, {
               hr: true,
               visible: function(item) {
                    if (item.t == 'dir') {
                         var specialDirs = ['websockets', 'forms', 'modules', 'commands', 'controllers', 'model'];
                         if (specialDirs.indexOf(item.n) >= 0) {
                              return true;
                         }
                    }
                    return false;
               },
          }, {
               icon: "fa fa-fw fa-file-text-o",
               label: "New File",
               click: function(item) {
                    $timeout(function() {
                         var name = prompt("New file name:");
                         if (!name) return;
                         
                         var path = item.p + '/' + name;
                         if (item.t != 'dir') {
                              path = item.p.substr(0, item.p.length - item.n.length) + name;
                         }
                         
                         $http.get(Yii.app.createUrl('/builder/tree/touch', {
                              path: path
                         })).then(function(res) {
                              var cur = item;
                              if (item.t != 'dir') {
                                   cur = item.parent
                              }
                              
                              if (cur.childs) {
                                   cur.childs.splice(0, cur.childs.length);
                              }
                              $scope.expand(cur, function() {
                                   for (var i in item.childs) {
                                        if (item.childs[i].p == res.data) {
                                             $timeout(function() {
                                                  $scope.select(item.childs[i]);
                                                  $scope.open(item.childs[i]);
                                             });
                                             break;
                                        }
                                   }
                              });
                         });
                    });
               }
          }, {
               icon: "fa fa-fw fa-folder-o",
               label: "New Folder",
               click: function(item) {
                    var name = prompt("New Folder Name:")
                    if (name != "") {
                         if (item.t == 'dir') {
                              $http.get(Yii.app.createUrl('/builder/tree/mkdir', {
                                   dir: item.p + '/' + name
                              })).then(function(res) {
                                   if (item.childs) {
                                        item.childs.splice(0, item.childs.length);
                                   }
                                   $scope.expand(item, function() {
                                        for (var i in item.childs) {
                                             if (item.childs[i].n == name) {
                                                  $timeout(function() {
                                                       $scope.select(item.childs[i]);
                                                  });
                                                  break;
                                             }
                                        }
                                   });
                              });
                         }
                    }
               }
          }, {
               hr: true,
          }, {
               label: "Refresh",
               visible: function(item) {
                    return item.t == 'dir';
               },
               click: function(item) {
                    if (item.childs) {
                         item.childs.splice(0, item.childs.length);
                    }
                    $scope.expand(item);
               }
          }, {
               label: "Rename",
               click: function(item) {}
          }, {
               label: "Delete",
               click: function(item) {
                    if (confirm("Are You sure? this cannot be undone!")) {
                         item.loading = true;
                         item.n = 'Deleting...'
                         $http.get(Yii.app.createUrl('/builder/tree/rmrf', {
                              path: item.p
                         })).then(function(res) {
                              item.parent.childs.splice(item.idx, 1)
                              item.parent.childs.forEach(function(i, key) {
                                   i.idx = key;
                              })
                         });
                    }
               }
          }]
     }
     $scope.showContextMenu = function(item, x, y) {
          $scope.cm.active = item;
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
     $(".tree-container").mousemove(function(e) {
          if ($scope.drag.el) {
               var offset = 20
               if (e.clientY < $(".tree").offset().top + offset) {
                    if (!$(".tree").is(':animated')) {
                         $(".tree").stop(true, false).animate({
                                   scrollTop: 0
                              },
                              $(".tree").scrollTop());
                    }
               }
               else
               if (e.clientY > $(".tree").offset().top + $('.tree').height() - offset) {
                    if (!$(".tree").is(':animated')) {
                         $(".tree").stop(true, false).animate({
                                   scrollTop: $(".tree")[0].scrollHeight
                              },
                              $(".tree")[0].scrollHeight);
                    }
               }
               else {
                    $(".tree").stop(true, false);
               }
          }
          else {
               $(".tree").stop(true, false);
          }
     });
     $(window).mousemove(function(e) {
          if ($scope.drag.el) {
               $($scope.drag.el).css({
                    position: 'absolute',
                    zIndex: 9999,
                    left: (e.clientX + 15) + 'px',
                    top: e.clientY + 'px',
               });
          }
     });
     $(window).mouseup(function(e) {
          if ($scope.drag.el) {
               $scope.drag.el.remove();
               $scope.drag.el = false;
               $scope.drag.item = null;
               $scope.drag.inititem = null;
          }
          $(".tree-item.draghover").removeClass('draghover');
     });
     $scope.itemMouseDown = function(e, item) {
          e.preventDefault();
          e.stopPropagation();
          $scope.drag.inititem = item;
          if (e.which == 1) {
               $scope.drag.item = item;
          }
          $scope.drag.touchTimeout = $timeout(function() {
               $scope.drag.item = false;
               $scope.showContextMenu(item, e.clientX, e.clientY);
          }, 600);
     }
     $scope.itemMouseOver = function(e, item) {
          if ($scope.drag.el) {
               $(".tree-item.draghover").removeClass('draghover');
               if (item.t == 'dir') {
                    var el = e.target;
                    if (!$(e.target).hasClass('tree-item')) {
                         el = $(e.target).parents('.tree-item');
                    }
                    $(el).addClass('draghover');

                    if ($scope.drag.expandTimeout) {
                         $timeout.cancel($scope.drag.expandTimeout);
                    }
                    $scope.drag.expandTimeout = $timeout(function() {
                         $scope.expand(item);
                    }, 600);
               }
          }
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
               if (!$(e.target).hasClass('tree-item')) {
                    el = $(e.target).parents('.tree-item');
               }

               $scope.drag.el = $(el).clone();
               $scope.drag.el.addClass('dragging');
               $scope.drag.el.appendTo('body');
          }
     }
     $scope.itemMouseUp = function(e, item) {
          e.preventDefault();
          e.stopPropagation();
          $(".tree-item.draghover").removeClass('draghover');
          if ($scope.drag.touchTimeout) {
               $timeout.cancel($scope.drag.touchTimeout);
          }
          if (!$scope.drag.inititem) return;
          if ($scope.drag.el) {
               $scope.drag.el.remove();
               $scope.drag.el = false;
               $scope.drag.item = null;
               return;
          }

          if (item.id == $scope.drag.inititem.id) {
               $scope.drag.item = null;
               $scope.drag.inititem = null;
               switch (e.which) {
                    case 1: // this is left click
                         $scope.select(item);
                         $scope.open(item);
                         $timeout(function() {
                              $scope.resetSearch();
                         })
                         break;
                    case 2: // this is middle click
                         break;
                    case 3: // this is right click
                         $scope.showContextMenu(item, e.clientX, e.clientY);
                         break;
                    default:
                         alert("you have a strange mouse!");
                         break;
               }
          }
     }
     $http.get(Yii.app.createUrl('/builder/tree/ls')).then(function(res) {
          $scope.tree = res.data;
          $scope.tree.forEach(function(item, key) {
               item.idx = key;
               item.parent = $scope.tree;
          });
     });
     $scope.detailPathChanged = function(e) {
          if (e.keyCode == 13) {
               $scope.search.detail.show = false;
               $scope.doSearch();
          }
     }
     $scope.showSearchResult = function() {
          var maxitem = $scope.search.paging.init;
          $scope.search.tree = [];
          $scope.search.loading = true;
          $timeout(function() {
               for (var i in $scope.search.rawtree) {
                    if (i < maxitem) {
                         $scope.search.tree.push($scope.search.rawtree[i]);
                    }
               }
               if ($scope.search.timeout) {
                    $timeout.cancel($scope.search.timeout);
               }

               $scope.search.timeout = $timeout(function() {
                    $scope.nextSearchResult();
               }, 200);
          });
     }
     $scope.nextSearchResult = function() {
          $scope.search.loading = true;
          var oldlength = $scope.search.tree.length;
          var maxitem = $scope.search.tree.length + $scope.search.paging.next;
          for (var i in $scope.search.rawtree) {
               if (i >= oldlength && $scope.search.tree.length < maxitem) {
                    $scope.search.tree.push($scope.search.rawtree[i]);
               }
          }

          $timeout(function() {
               $scope.search.loading = false;
          });
     }
     $scope.resetSearch = function() {
          if ($scope.search.text && $scope.selected) {
               $scope.expandToItem($scope.selected);
          }
          $scope.search.text = '';
          $scope.search.loading = false;
          $(".search-file input").focus();
     }
     $scope.doSearch = function() {
          $scope.search.loading = true;

          if ($scope.search.detail.show) {
               $scope.search.detail.show = false;
          }

          if ($scope.search.text) {
               if ($scope.search.canceler) {
                    $scope.search.canceler.resolve();
               }
               $scope.search.canceler = $q.defer();
               $http.get(Yii.app.createUrl('/builder/tree/search', {
                    n: $scope.search.text,
                    dir: $scope.search.detail.path
               }), {
                    timeout: $scope.search.canceler
               }).then(function(res) {
                    $scope.search.rawtree = res.data;
                    $scope.search.loading = false;
                    $scope.showSearchResult();
                    $scope.search.canceler = false;
               });
          }
          else {
               $scope.search.loading = false;
          }
     }
     $scope.expand = function(item, callback) {
          item.expand = true;

          function expandFirstChild(item) {
               if (item.childs && item.childs.length == 1) {
                    if (item.childs[0].t == 'dir') {
                         $scope.expand(item.childs[0]);
                    }
               }
          }
          if (!item.childs || item.childs.length == 0) {
               item.loading = true;
               $http.get(Yii.app.createUrl('/builder/tree/ls&dir=' + item.d))
                    .then(function(res) {
                         item.loading = false;

                         function addChild(data, increment) {
                              var ins = data.splice(0, increment || 10);
                              ins.forEach(function(i, key) {
                                   if (!item.childs) {
                                        item.childs = [];
                                   }
                                   i.parent = item;
                                   i.idx = key;
                                   item.childs.push(i);
                              });

                              if (data.length > 0) {
                                   $timeout(function() {
                                        addChild(data, (increment || 10) + 10);
                                   });
                              }
                              else {
                                   if (!callback) {
                                        expandFirstChild(item);
                                   }
                                   else {
                                        callback(item);
                                   }
                              }
                         }
                         addChild(res.data);
                    });
          }
          else {
               if (!callback) {
                    expandFirstChild(item);
               }
               else {
                    callback(item);
               }
          }
     }
     $scope.expandToItem = function(item, callback) {
          var path = item.d.split('/');

          function expandSingle(tree, name, callback) {
               tree.forEach(function(i) {
                    if (i.t == 'dir' && i.n == name) {
                         if (i.childs) {
                              i.expand = true;
                              callback(i);
                         }
                         else {
                              $scope.expand(i, callback);
                         }
                    }
                    else if (i.id == item.id) {
                         callback(i);
                    }
               });
          }
          var tree = $scope.tree;
          var idx = 0;

          function recursiveExpand() {
               expandSingle(tree, path[idx], function(item) {
                    tree = item.childs;
                    idx++;
                    if (idx < path.length - 1) {
                         recursiveExpand();
                    }
                    else {
                         for (var i = 0; i < 5; i++) {
                              $timeout(function() {
                                   $(".tree .active")[0].scrollIntoView();
                              }, 100);
                         }

                         if (callback) {
                              callback();
                         }
                    }
               });
          }

          if ($(".tree").length == 0) {
               $scope.builder.hideTree = false;

               function expandWhenReady() {
                    if ($(".tree").length == 0) {
                         $timeout(function() {
                              expandWhenReady();
                         }, 100);
                    }
                    else {
                         $timeout(function() {
                              recursiveExpand();
                         }, 100);
                    }
               }
               expandWhenReady();
          }
          else {
               recursiveExpand();
          }
     }
     $scope.shrink = function(item) {
          item.loading = false;
          item.expand = false;
     }
     $scope.select = function(item) {
          $scope.selected = item;

          if ($scope.search.detail.show) {
               $scope.search.detail.show = false;
          }
     }
     $scope.open = function(item) {
          if (item.t == 'dir') {
               $scope.arrowToggle(item);
          }
          else {
               window.tabs.open(item);
          }
     }
     $scope.arrowToggle = function(item) {
          if (!item.expand) {
               $scope.expand(item);
          }
          else {
               $scope.shrink(item);
          }
     }
     $scope.getArrow = function(item) {
          if (item.t == 'dir') {
               if (!item.expand) {
                    return 'fa-caret-right';
               }
               else {
                    return 'fa-caret-down';
               }
          }
     }
     $scope.getIcon = function(item) {
          if (item.t == 'dir') {
               if (item.expand) {
                    return 'folder-open.png';
               }
               else {
                    return 'folder.png';
               }
          }

          var exts = {
               'php': 'application-x-php.png',
               'js': 'application-javascript.png',
               'css': 'text-x-css.png',
               'json': 'application-json.png',
               'exe': 'application-executable.png',
               'txt': 'text-x-changelog.png',
               'bat': 'application-executable.png',
               'gitignore': 'extension.png'
          }

          if (exts[item.ext]) {
               return exts[item.ext];
          }
          else {
               return 'application-document.png';
          }
     }
     $scope.treeMode = $('.tree-container').attr('mode');
     $scope.showItem = function(item) {
          if ($scope.treeMode == 'plansys') {
               return true;
          }
          else {
               var hiddenItems = [
                    'app/config',
                    'index.php',
                    'plansys'
               ];

               if (hiddenItems.indexOf(item.d) >= 0) {
                    return false;
               }
               else {
                    return true;
               }
          }
     }
});