/* global Yii, $scope, $http, $timeout, app, builder, PopupCenter, $ */

app.controller("Tree", function($scope, $http, $timeout, $q) {
     window.tree = $scope;
     $scope.selected = null;
     $scope.tree = [];

     $scope.isUnsaved = function(item) {
          var idx = window.tabs.findTab(item);
          if (idx !== false) {
               return window.tabs.list[idx].unsaved;
          }
     }

     $scope.treebar = {
          list: JSON.parse($("#tree-bar-data").text()),
          active: $('.tree-container').attr('treebar-active') || 'form',
          tree: {},
          root: {},
          loading: false,
          switch: function(mode, callback) {
               $scope.treebar.active = mode;
               $scope.tree = $scope.treebar.tree[$scope.treebar.active];
               $scope.resetSearch();

               window.builder.set('tree.treebar.active', mode);
               if ($scope.treebar.tree[$scope.treebar.active].length == 0) {
                    $scope.treebar.loading = true;
                    var tactive = $scope.treebar.active;
                    $http.get(Yii.app.createUrl('/builder/tree/ls&mode=' + tactive))
                         .then(function(res) {
                              $scope.treebar.loading = false;
                              $scope.treebar.tree[tactive] = res.data;
                              $scope.treebar.root[tactive] = {
                                   childs: $scope.tree,
                                   t: 'dir'
                              }
                              $scope.treebar.tree[tactive].forEach(function(item, key) {
                                   if (!$scope.treebar.root[tactive].p) {
                                        $scope.treebar.root[tactive].p = item.p.substr(0, item.p.length - item.n.length - 1);
                                   }
                                   item.idx = key;
                                   if (!item.parent) {
                                        item.parent = {};
                                   }
                                   item.parent[tactive] = $scope.treebar.root[tactive];
                              });
                              $scope.tree = $scope.treebar.tree[tactive];

                              if (typeof callback == "function") callback();
                              $timeout(function() {
                                   $scope.initCount = 0;
                                   var initExpand = function(tree, tdir) {
                                        var shouldExpand = function(item) {
                                             if (item.parent[tactive] == $scope.treebar.root[tactive]) {
                                                  var res = (tdir[item.d]);
                                                  if (res) {
                                                       $scope.init = true;
                                                       $scope.initCount++;
                                                  }
                                                  return res;
                                             }
                                             else if (item.parent[tactive].d) {
                                                  if (!tdir) return false;

                                                  var res = tdir[item.d.substr(item.parent[tactive].d.length + 1)];

                                                  if (res) {
                                                       $scope.init = true;
                                                       $scope.initCount++;
                                                  }
                                                  return res;
                                             }
                                        }
                                        tree.forEach(function(item) {
                                             if (item.t == "dir") {
                                                  if (shouldExpand(item)) {
                                                       $scope.expand(item, function() {
                                                            $scope.initCount--;
                                                            if (item.childs) {
                                                                 var d = item.d;

                                                                 if (!item.parent[tactive]) {
                                                                      console.log(item, tactive);
                                                                      return;
                                                                 }
                                                                 if (item.parent[tactive].d) {
                                                                      d = item.d.substr(item.parent[tactive].d.length + 1);
                                                                 }
                                                                 initExpand(item.childs, tdir[d]);
                                                            }

                                                            if ($scope.initCount <= 0) {
                                                                 $scope.init = false;
                                                            }
                                                       });
                                                  }
                                             }
                                        })
                                   }
                                   initExpand($scope.tree, $scope.expanded[tactive]);
                              })
                         })
                         .catch(function() {
                              $scope.treebar.loading = false;
                              alert("Loading tree failed. Please check your connection.");
                         });
               }
               else {
                    if (typeof callback == "function") callback();
               }
          }
     };
     $scope.treebar.list.forEach(function(item) {
          $scope.treebar.tree[item] = [];
          $scope.treebar.root[item] = {};
     });
     $timeout(function() {
          $scope.treebar.switch($scope.treebar.active);
     });

     if ($("#tree-expand-data").text().trim() != "") {
          $scope.expanded = JSON.parse($("#tree-expand-data").text());
     }
     if (!$scope.expanded) {
          $scope.expanded = {};
          $scope.treebar.list.forEach(function(item) {
               $scope.expanded[item] = {};
          })
     }

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
          showDetail: function() {
               if ($scope.treebar.active == 'file') {
                    $scope.search.detail.show = true
               }
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
               icon: "fa fa-file-text-o",
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
                         }).catch(function() {
                              item.loading = false;
                              alert("Failed to create file");
                         });
                    });
               }
          }, {
               icon: "fa fa-folder-o",
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
                              }).catch(function() {
                                   item.loading = false;
                                   alert("Failed to create directory");
                              });
                         }
                    }
               }
          }, {
               hr: true,
          }, {
               icon: "fa fa-search",
               label: 'Search Here',
               visible: function(item) {
                    return item.t == 'dir' && $scope.treebar.active == 'file';
               },
               click: function(item) {
                    $scope.search.detail.path = '/' + item.d;
                    $(".search-text").focus();
               }
          }, {
               icon: "fa fa-refresh",
               label: "Refresh",
               visible: function(item) {
                    return item.t == 'dir';
               },
               click: function(item) {
                    $scope.refreshDir(item);
               }
          }, {
               hr: true,
          }, {
               icon: "fa fa-copy",
               label: function(item) {
                    return "Copy ";
               },
               click: function(item) {
                    $scope.copiedItem = item;
               }
          }, {
               icon: "fa fa-paste",
               label: function(item) {
                    var itn = $scope.copiedItem;
                    var dot = itn.n.length > 10 ? "..." : "";
                    return "Paste <b class='paste-item' tooltip='" + itn.n + "'>" + itn.n.substr(0, 10) + dot + "</b>";
               },
               visible: function(item) {
                    return !!$scope.copiedItem && item.t == 'dir';
               },
               click: function(item) {
                    alert("Maaf, masi belum bisa kopas...");
                    $scope.copiedItem = null;
               }
          }, {
               hr: true,
          }, {
               icon: "fa fa-pencil-square-o",
               label: "Rename",
               click: function(item) {
                    var newname = prompt("New file name:", item.n);
                    var path = item.p.split("/");
                    path.pop();
                    path = path.join("/");
                    item.loading = true;
                    $http.get(Yii.app.createUrl('/builder/tree/mv', {
                         from: item.p,
                         to: path + '/' + newname
                    })).then(function(res) {
                         item.d = res.data;
                         item.n = newname;
                         item.p = path + '/' + newname;
                         item.ext = newname.split(".").pop();
                         item.loading = false;
                    }).catch(function() {
                         item.loading = false;
                         alert("Failed to rename " + item.t);
                    });
               }
          }, {
               icon: "fa fa-eraser",
               label: "Delete",
               visible: function(item) {
                    if (typeof item.removable != "undefined" && !item.removable) return false;
                    return true;
               },
               click: function(item) {
                    if (confirm("Are you sure? this cannot be undone!")) {
                         item.loading = true;
                         var oldname = item.n;
                         item.n = 'Deleting...';
                         $http.get(Yii.app.createUrl('/builder/tree/rmrf', {
                              path: item.p
                         })).then(function(res) {
                              window.tabs.close(item);
                              item.parent[$scope.treebar.active].childs.splice(item.idx, 1)
                              item.parent[$scope.treebar.active].childs.forEach(function(i, key) {
                                   i.idx = key;
                              })
                         }).catch(function() {
                              item.loading = false;
                              item.n = oldname;
                              alert("Failed to delete " + item.t);
                         });
                    }
               }
          }]
     }
     $scope.refreshDir = function(item, f) {
          if (item.childs) {
               item.childs.splice(0, item.childs.length);
          }
          $scope.expand(item, f);
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
     $scope.resetDrag = function(e) {
          if ($scope.drag.el) {
               $scope.drag.el.remove();
               $scope.drag.el = false;
               $scope.drag.item = null;
               $scope.drag.inititem = null;
          }
          $(".tree-item.draghover").removeClass('draghover');
     };
     $(window).keyup(function(e) {
          if (e.keyCode == 27) {
               $scope.resetDrag();
          }
     })
     $(window).mouseup($scope.resetDrag);
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

                    if ($scope.drag.expandTimeout && $scope.drag.lastHoverItem != item) {
                         $timeout.cancel($scope.drag.expandTimeout);
                         $scope.drag.expandTimeout = false;
                    }

                    if (!$scope.drag.expandTimeout) {
                         $scope.drag.expandTimeout = $timeout(function() {
                              $scope.expand(item);
                              $scope.drag.expandTimeout = false;
                         }, 400);
                    }
                    $scope.drag.lastHoverItem = item;
               }
               else {
                    $scope.drag.lastHoverItem = item.parent;
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
               var from = $scope.drag.item;
               var to = $scope.drag.lastHoverItem;
               if (from && to) {
                    if (to != $scope.treebar.root[$scope.treebar.active]) {
                         if (to.t != 'dir') {
                              to = to.parent;
                         }
                    }
                    var mv = function() {
                         if (to.loading) {
                              $timeout(mv, 500);
                              return;
                         }

                         to.loading = true;

                         $http.get(Yii.app.createUrl('/builder/tree/mv', {
                              from: from.p,
                              to: to.p + '/' + from.n
                         })).then(function(res) {
                              $scope.drag.lastHoverItem = false;
                              from.parent[$scope.treebar.active].childs.forEach(function(i, key) {
                                   i.idx = key;
                              })
                              from.parent[$scope.treebar.active].childs.splice(from.idx, 1)
                              from.parent[$scope.treebar.active].childs.forEach(function(i, key) {
                                   i.idx = key;
                              })
                              from.parent[$scope.treebar.active] = to;
                              from.p = to.p + '/' + from.n;
                              from.d = res.data;

                              if (from.t == "dir") {
                                   var expanded = from.expanded;
                                   $scope.refreshDir(from, function() {
                                        if (!expanded) {
                                             $scope.shrink(from);
                                        }
                                   });
                              }

                              if (!to.childs) {
                                   $scope.expand(to);
                              }
                              else {
                                   to.loading = false;

                                   if (!$scope.isArray(to.childs)) {
                                        to.childs = [];
                                   }
                                   if (from.t != 'dir') {
                                        to.childs.push(from);
                                   }
                                   else {
                                        to.childs.unshift(from);
                                   }
                                   to.childs.forEach(function(i, key) {
                                        i.idx = key;
                                   });
                              }
                         }).catch(function() {
                              to.loading = false;
                              $scope.drag.lastHoverItem = false;

                              alert("Failed to move " + from.t);
                         });
                    }
                    if (from && to && from.p != to.p + '/' + from.n) {
                         mv();
                    }
                    else {
                         $scope.drag.lastHoverItem = false;
                    }
               }

               $scope.drag.el.remove();
               $scope.drag.el = false;
               $scope.drag.item = null;
               return;
          }

          if (item == $scope.treebar.root[$scope.treebar.active]) {
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
     $scope.detailPathChanged = function(e) {
          if (e.keyCode == 13) {
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
          if ($scope.search.detail.show) {
               $scope.search.detail.show = false;
          }
     }
     $scope.doSearch = function() {
          $scope.search.loading = true;

          if ($scope.search.text) {
               if ($scope.search.canceler) {
                    $scope.search.canceler.resolve();
               }
               $scope.search.canceler = $q.defer();
               $http.get(Yii.app.createUrl('/builder/tree/search', {
                    n: $scope.search.text,
                    dir: $scope.search.detail.path,
                    mode: $scope.treebar.active
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
          if (item.expanding) {
               if (callback) {
                    item.expanding.push(callback);
               }
               return;
          }
          item.expand = true;
          if (typeof item.expanding == "undefined") {
               item.expanding = [];
          }

          function expandFirstChild(item) {
               delete item.expanding;
               if (item.childs && item.childs.length == 1) {
                    if (item.childs[0].t == 'dir') {
                         $scope.expand(item.childs[0]);
                    }
               }
          }

          function doneExpanding(item) {
               if ($scope.isArray($scope.expanded[$scope.treebar.active])) {
                    $scope.expanded[$scope.treebar.active] = {};
               }

               var tdirs = $scope.getPathFromItem(item);
               var tdir = $scope.expanded[$scope.treebar.active];
               tdirs.forEach(function(dir) {
                    if (!tdir.$length) {
                         tdir.$length = 0;
                    }

                    if (!tdir[dir]) {
                         tdir[dir] = {}
                         tdir.$length++;
                    }

                    tdir = tdir[dir];
               });

               if (typeof item.expanding != "undefined") {
                    if ($scope.isArray(item.expanding) && item.expanding.length == 0) {
                         delete item.expanding;
                    }
               }

               if (!callback) {
                    if (!item.expanding) {
                         expandFirstChild(item);
                    }
               }
               else {
                    callback(item);
               }
               if (item.expanding) {
                    $scope.expand(item, item.expanding.shift());
               }
               if (!$scope.init) {
                    window.builder.set('tree.expand', $scope.expanded);
               }
          }

          if (!item.childs || item.childs.length == 0) {
               item.loading = true;

               $http.get(Yii.app.createUrl('/builder/tree/ls&dir=' + item.d + '&mode=' + $scope.treebar.active))
                    .then(function(res) {
                         var incrementPage = 5;

                         function addChild(data, increment) {
                              var ins = data.splice(0, increment || incrementPage);
                              ins.forEach(function(i, key) {
                                   if (!item.childs) {
                                        item.childs = [];
                                   }
                                   if (!i.parent) {
                                        i.parent = {};
                                   }
                                   i.parent[$scope.treebar.active] = item;
                                   i.idx = key;
                                   item.childs.push(i);
                              });

                              if (data.length > 0) {
                                   $timeout(function() {
                                        addChild(data, (increment || incrementPage) + incrementPage);
                                   });
                              }
                              else {
                                   doneExpanding(item);
                                   item.loading = false;
                              }
                         }
                         addChild(res.data);
                    })
                    .catch(function(res) {
                         item.loading = false;
                         doneExpanding(item);
                    });
          }
          else {
               doneExpanding(item);
          }
     }

     $scope.getPathFromItem = function(item) {
          function match(str, rule) {
               return new RegExp("^" + rule.split("*").join(".*") + "$").test(str);
          }
          switch ($scope.treebar.active) {
               case "file":
                    return item.d.split("/");
                    break;
               case "form":
                    var prefix = [
                         "app/forms/*",
                         "plansys/forms/*",
                         "app/modules/*/forms",
                         "app/modules/*/forms",
                         "app/modules/*/forms/*",
                         "app/modules/*/forms/*",
                    ]
                    var ismatch = false;
                    for (p in prefix) {
                         if (match(item.d, prefix[p])) {
                              ismatch = prefix[p];
                              break;
                         }
                    }
                    if (ismatch) {
                         var itemd = item.d.split("/");
                         if (ismatch.split("*").length >= 2) {
                              var root = itemd.splice(0, 4).join("/");
                         }
                         else {
                              var root = itemd.splice(0, 2).join("/");
                         }
                         itemd.unshift(root);
                         return itemd;
                    }
                    break;
               case "model":
                    var prefix = [
                         "app/models",
                         "plansys/models",
                         "app/models/*",
                         "plansys/models/*",
                    ]
                    var ismatch = false;
                    for (p in prefix) {
                         if (match(item.d, prefix[p])) {
                              ismatch = prefix[p];
                              break;
                         }
                    }
                    if (ismatch) {
                         var itemd = item.d.split("/");
                         var root = itemd.splice(0, 2).join("/");
                         itemd.unshift(root);
                         return itemd;
                    }
                    break;
               case "controller":
                    var prefix = [
                         "app/controllers",
                         "plansys/controllers",
                         "app/modules/*/controllers",
                         "plansys/modules/*/controllers",
                         "app/modules/*/controllers/*",
                         "plansys/modules/*/controllers/*",
                    ]
                    var ismatch = false;
                    for (p in prefix) {
                         if (match(item.d, prefix[p])) {
                              ismatch = prefix[p];
                              break;
                         }
                    }
                    if (ismatch) {
                         var itemd = item.d.split("/");

                         if (ismatch.split("*").length >= 2) {
                              var root = itemd.splice(0, 4).join("/");
                         }
                         else {
                              var root = itemd.splice(0, 2).join("/");
                         }
                         itemd.unshift(root);
                         return itemd;
                    }
                    break;
               case "module":
                    var prefix = [
                         "app/modules/*",
                         "plansys/modules/*",
                    ]
                    var ismatch = false;
                    for (p in prefix) {
                         if (match(item.d, prefix[p])) {
                              ismatch = prefix[p];
                              break;
                         }
                    }
                    if (ismatch) {
                         var itemd = item.d.split("/");
                         var root = itemd.splice(0, 2).join("/");
                         itemd.unshift(root);
                         return itemd;
                    }
                    break;
          }
          return [];
     }

     $scope.expandToItem = function(item, callback) {
          var path = $scope.getPathFromItem(item);
          var ocb = callback;

          function expandSingle(tree, dir, callback) {
               if (!$scope.isArray(tree)) {
                    return;
               }
               var found = false;
               for (var tidx in tree) {
                    var i = tree[tidx];
                    var d = dir;
                    if (i.parent[$scope.treebar.active] && i.parent[$scope.treebar.active].d) {
                         d = i.parent[$scope.treebar.active].d + '/' + dir
                    }
                    if (i.t == 'dir' && i.d == d) {
                         found = true;
                         if (i.childs) {
                              i.expand = true;
                              callback(i);
                         }
                         else {
                              $scope.expand(i, callback);
                         }
                    }
                    else if (i.id == item.id) {
                         found = true;
                         callback(i);
                    }
               }

               if (!found && tree == $scope.tree && $scope.treebar.active != 'file') {
                    $scope.treebar.switch('file', function() {
                         $scope.expandToItem(item, ocb);
                    });
               }
          }
          var tree = $scope.tree;
          var idx = 0;
          var rootItem = item;

          function recursiveExpand() {
               expandSingle(tree, path[idx], function(item) {
                    tree = item.childs;
                    idx++;

                    if (idx <= path.length - 1) {
                         recursiveExpand()
                    }
                    else {
                         $scope.selected = rootItem;
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
               window.builder.hideTree = false;

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

          var tparent = item;
          var dirs = $scope.getPathFromItem(item);
          var tdir = $scope.expanded[$scope.treebar.active];
          while (dirs.length > 0) {
               for (var i in dirs) {
                    var dir = dirs[i];
                    if (i == dirs.length - 1) {
                         tdir.$length--;
                         delete tdir[dir];
                         dirs.pop();
                         break;
                    }

                    if (!!tdir[dir]) {
                         tdir = tdir[dir];
                    }
                    else {
                         var len = 0;
                         for (var idx in tdir) {
                              len++;
                         }
                         tdir.$length = len;

                         dirs = dirs.splice(0, i + 1);
                         break;
                    }
               };
          }
          window.builder.set('tree.expand', $scope.expanded);
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
               function getReadableSize(fileSizeInBytes) {
                    var i = -1;
                    var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
                    do {
                         fileSizeInBytes = fileSizeInBytes / 1024;
                         i++;
                    } while (fileSizeInBytes > 1024);

                    return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];
               };

               $http.get(Yii.app.createUrl('/builder/tree/getsize&dir=' + item.d))
                    .then(function(res) {
                         var shouldOpen = true;
                         var size = parseInt(res.data);
                         item.size = size / 1024;
                         if (item.size > 200) {
                              shouldOpen = confirm("File size is " + getReadableSize(size) + ", continue opening ?");
                         }
                         if (shouldOpen) {
                              window.tabs.open(item);
                         }
                    });
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