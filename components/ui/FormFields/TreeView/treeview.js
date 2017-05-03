app.directive('treeView', function($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                var map = $scope.map = JSON.parse($el.find("script[name=map]:eq(0)").text());
                $scope.tree = JSON.parse($el.find("script[name=data]:eq(0)").text());
                $scope.renderID = $el.find("script[name=id]:eq(0)").text();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.class = $el.find("data[name=class_alias]").text().trim();
                $scope.selected = null;
                $scope.drag = {
                    item: null
                };

                var parent = $scope.getParent($scope);
                parent[$scope.name] = $scope;

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function() {
                        $scope.selected = ctrl.$viewValue;
                    };
                }

                $scope.selectItem = function(item) {
                    $scope.selected = item;
                    ctrl.$setViewValue($scope.flattenTree($scope.selected));
                    
                    if (!item.$expand) {
                        $scope.expand(item);
                    }
                    else {
                        $scope.shrink(item);
                    }
                }

                $scope.flattenTree = function(item) {
                    function iterate(obj, stack) {
                        var childs = [];
                        for (var property in obj) {
                            if (obj.hasOwnProperty(property)) {

                                if (property[0] === '$') {
                                    delete obj[property];
                                }

                                if (typeof obj[property] == "object") {
                                    childs.push({
                                        obj: obj[property],
                                        key: stack + '.' + property
                                    })
                                }
                            }
                        }
                        
                        for (var c in childs) {
                            iterate(childs[c].obj, childs[c].key);
                        }
                    }
                    if (typeof item == "undefined") {
                        var ftree = angular.copy($scope.tree);
                    } else {
                        var ftree = angular.copy(item); 
                    }
                    
                    iterate(ftree, '');
                    return ftree;
                }
                $scope.getArrow = function(item) {
                    if ($scope.isArray(item.items)) {
                        if (!item.$expand) {
                            return 'fa-caret-right';
                        }
                        else {
                            return 'fa-caret-down';
                        }
                    }
                }
                $scope.expandAll = function(f, fend, items, pending) {
                    if (typeof items == "undefined") {
                        items = $scope.tree;
                    }
                    
                    if (typeof pending == "undefined") {
                        pending = items.length;
                        console.log(pending);
                    }
                    
                    var isDone = function() {
                        if (pending <= 0) {
                            if (typeof fend == "function") {
                                fend();
                            }
                        }
                    }
                    
                    for (var i in items) {
                        var item = items[i];
                        
                        if (item[map.canExpand]) {
                            $scope.expand(item, function() {
                                pending--;
                                var continueExpand = true;
                                if (typeof f == "function") {
                                    if (f(item) ===  false) {
                                        continueExpand = false;
                                    }
                                }
                                
                                if (continueExpand) {
                                    pending += item[map.items].length;
                                    $scope.expandAll(f, fend, item[map.items], pending);
                                }
                                
                                isDone();
                            });
                        } else {
                            pending--;
                            var continueExpand = true;
                            if (typeof f == "function") {
                                if (f(item) ===  false) {
                                    continueExpand = false;
                                }
                            }
                            
                            if (($scope.isArray(item[map.items]) && item[map.items].length > 0)) {
                                if (continueExpand) {
                                    pending += item[map.items].length;
                                    $scope.expandAll(f, fend, item[map.items], pending);
                                }
                            }
                            
                            isDone();
                        }
                    }
                }
                $scope.selectBy = function(col, val) {
                    if (typeof val == "undefined") {
                        val = col;
                        col = map.id;
                    }
                    
                    $scope.expandAll(function(item) {
                        if (item[col] == val) {
                            $scope.selectItem(item);
                            return false;
                        }
                    });
                }
                $scope.expand = function(item, callback) {
                    if (!item[map.canExpand]) return;
                    item.$expand = true;

                    function expandFirstChild(item) {
                        if (item[map.canExpand] && item[map.items].length == 1) {
                            if (item[map.items][0] && item[map.items][0][map.canExpand]) {
                                $scope.expand(item[map.items][0]);
                            }
                        }
                    }

                    item.$loading = true;
                    var post = {};
                    for (var i in item) {
                        if (i[0] != "$" && i != map.items) {
                            post[i] = item[i];
                        }
                    }
                    $http.post(Yii.app.createUrl('/formfield/TreeView.expand'), {
                        c: $scope.class,
                        n: $scope.name,
                        item: post
                    }).then(function(res) {
                        item.$loading = false;

                        if ($scope.isArray(item[map.items])) {
                            item[map.items].splice(0, item[map.items].length);
                        }

                        function addChild(data, increment) {
                            var ins = data.splice(0, increment || 10);
                            ins.forEach(function(i, key) {
                                if (!item[map.items]) {
                                    item[map.items] = [];
                                }
                                i.$parent = item;
                                i.$idx = key;
                                item[map.items].push(i);
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
                $scope.shrink = function(item) {
                    if (!item[map.canExpand]) return;
                    item.$loading = false;
                    item.$expand = false;
                }
                $scope.itemMouseDown = function(e, item) {
                    e.preventDefault();
                    e.stopPropagation();
                    $scope.drag.inititem = item;
                    if (e.which == 1) {
                        $scope.drag.item = item;
                    }
                    $scope.drag.touchTimeout = $timeout(function() {
                        $scope.drag.item = false;
                    }, 600);
                }
                $scope.itemMouseOver = function(e, item) {
                    e.preventDefault();
                    e.stopPropagation();
                    if ($scope.drag.el) {
                        $(".tree-view-item.draghover").removeClass('draghover');
                        if (item[map.items]) {
                            var el = e.target;
                            if (!$(e.target).hasClass('tree-view-item')) {
                                el = $(e.target).parents('.tree-view-item');
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
                    if ($scope.drag.item && item[map.id] != $scope.drag.item && !$scope.drag.el) {
                        if ($scope.drag.touchTimeout) {
                            $timeout.cancel($scope.drag.touchTimeout);
                        }

                        var el = e.target;
                        if (!$(e.target).hasClass('tree-view-item')) {
                            el = $(e.target).parents('.tree-view-item');
                        }

                        $scope.drag.el = $(el).clone();
                        $scope.drag.el.addClass('dragging');
                        $scope.drag.el.appendTo('body');
                    }
                }
                $scope.itemMouseUp = function(e, item) {
                    $(".tree-view-item.draghover").removeClass('draghover');
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

                    if (item[map.id] == $scope.drag.inititem[map.id]) {
                        $scope.drag.item = null;
                        $scope.drag.inititem = null;
                        switch (e.which) {
                            case 1: // this is left click
                                e.preventDefault();
                                e.stopPropagation();
                                $scope.selectItem(item);
                                break;
                            case 2: // this is middle click
                                break;
                            case 3: // this is right click
                                return true;
                                break;
                            default:
                                alert("you have a strange mouse!");
                                break;
                        }
                    }
                }
            }
        }
    }
});