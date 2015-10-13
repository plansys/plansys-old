app.directive('listView', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                $scope.parent = parent;
                $scope.itemChanging = false;

                // set default value
                $scope.loading = true;
                $scope.value = JSON.parse($el.find("data[name=value]").html().trim());
                $scope.modelClass = $el.find("data[name=model_class]").html().trim();
                $scope.fieldTemplate = $el.find("data[name=field_template]").html().trim();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.datasource = $scope.parent[$el.find("data[name=datasource]:eq(0)").text()];
                $scope.templateAttr = JSON.parse($el.find("data[name=template_attr]").html().trim());
                $scope.options = JSON.parse($el.find("data[name=options]").html().trim());

                // when ng-model is changed from inside directive
                $scope.updateListView = function () {
                    if (!!ctrl) {
                        $scope.itemChanging = true;
                        $scope.setViewValue();
                        $timeout(function () {
                            $scope.itemChanging = false;
                        }, 1000);
                    }
                };

                $scope.setViewValue = function () {
                    $scope.processValue(function () {
                        $scope.showUndoDelete = false;

                        var value = angular.copy($scope.value);
                        if ($scope.fieldTemplate == "default") {
                            var newVal = [];
                            for (i in value) {
                                newVal.push(value[i].val);
                            }
                            value = newVal;
                        }

                        ctrl.$setViewValue(value);
                    });
                };

                $scope.processValue = function (func) {
                    if (typeof $scope.options['unique'] == "string") {
                        $scope.options['unique'] = $scope.$eval($scope.options['unique']);
                    }

                    if ($scope.options['unique'] === true) {
                        if ($scope.value.length > 0 && typeof $scope.value[0] === "object") {
                            $scope.options['unique'] = [];
                            for (i in $scope.value[0]) {
                                if (i.indexOf("$") !== 0) {
                                    $scope.options['unique'].push(i);
                                }
                            }
                        }
                    }

                    if ($scope.isArray($scope.options['unique'])) {
                        if ($scope.value.length > 0) {
                            var unique = [];
                            for (i in $scope.value) {
                                var u = {};
                                var c = $scope.value[i];

                                for (ukey in $scope.options['unique']) {
                                    var key = $scope.options['unique'][ukey];
                                    u[key] = c[key] == '' || !c[key] ? '' : c[key];
                                }
                                u = JSON.stringify(u);
                                if (unique.indexOf(u) < 0) {
                                    unique.push(u);
                                } else {
                                    $scope.value.splice(i, 1);
                                }
                            }

                            if (typeof func == "function") {
                                func();
                            }
                        }
                    } else {
                        if (typeof func == "function") {
                            func();
                        }
                    }
                };

                $scope.showUndoDelete = false;
                $scope.deleted = {
                    idx: -1,
                    data: null
                };

                $scope.undo = function () {
                    if (!!$scope.deleted.data) {
                        $scope.value.splice($scope.deleted.idx, 0, $scope.deleted.data);
                        $scope.deleted = {
                            idx: -1,
                            data: null
                        };
                        $scope.updateListView();
                        $scope.showUndoDelete = false;
                    }
                };

                $scope.removeItem = function (index) {
                    var d = $scope.value.splice(index, 1);
                    $scope.deleted = {
                        idx: index,
                        data: d[0]
                    };
                    ctrl.$setViewValue(angular.copy($scope.value));

                    $scope.showUndoDelete = true;
                };

                $scope.addItem = function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if ($scope.value === null || typeof $scope.value === "undefined" || $scope.value === "") {
                        $scope.value = [];
                    }

                    var value = angular.extend({}, $scope.templateAttr);

                    //before add
                    var beforeAdd = $scope.options['ps-before-add'] || '';
                    if (beforeAdd != '' && typeof value != 'undefined') {
                        eval(beforeAdd);
                    }
                    $scope.value.push(value);
                    $scope.updateListView();
                    var valueID = $scope.value.length - 1;

                    $timeout(function () {
                        //after add
                        var afterAdd = $scope.options['ps-after-add'] || '';
                        value = $scope.value[valueID];
                        if (afterAdd != '' && typeof value != 'undefined') {
                            eval(afterAdd);
                        }

                        $timeout(function () {
                            if ($scope.value.length > 0) {
                                $scope.templateAttr = angular.copy($scope.value[$scope.value.length - 1]);
                            }
                        }, 10);
                    });
                };

                $scope.uiTreeOptions = {
                    dragStop: function (scope) {
                        $scope.updateListView();
                    },
                    accept: function (source, dest) {
                        if (source.$treeScope.$parent.name === dest.$treeScope.$parent.name) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                };

                var lastId = 0;

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.loading = true;

                            var value = ctrl.$viewValue;
                            if ($scope.fieldTemplate == "default") {
                                var newVal = [];
                                for (i in value) {
                                    newVal.push({val: value[i]});
                                }
                                value = newVal;
                            }

                            for (i in value) {
                                value[i].$$id = lastId++;
                            }

                            $scope.value = value;
                            $timeout(function () {
                                $scope.loading = false;
                            }, 0);
                        }
                    };
                }

                $scope.showListForm = function () {
                    $timeout(function () {
                        $el.find('.list-view-form li').show();
                    });
                }

                // change Watchers
                $timeout(function () {
                    if (!!$scope.options['ng-change']) {
                        $scope.stopWatchChanges = $scope.$watch('value', function (n, o) {
                            if (n !== o) {
                                if (!$scope.itemChanging) {
                                    $scope.setViewValue();
                                }
                            }
                        }, true);
                    }
                });

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        if ($scope.fieldTemplate == 'datasource') {
                            if (!!$scope.datasource) {
                                $scope.value = $scope.datasource.data;
                                $scope.datasource.beforeQueryInternal[$scope.renderID] = function () {
                                    $scope.loading = true;
                                }
                                $scope.datasource.afterQueryInternal[$scope.renderID] = function () {
                                    $scope.value = $scope.datasource.data;
                                    $timeout(function () {
                                        $scope.loading = false;
                                    }, 100);
                                }
                            }
                        } else {
                            var ngModelValue = $scope.$eval(attrs.ngModel);
                            if (typeof ngModelValue != "undefined") {
                                if ($scope.fieldTemplate == "default") {
                                    var newVal = [];
                                    for (i in ngModelValue) {
                                        newVal.push({val: ngModelValue[i]});
                                    }
                                    ngModelValue = newVal;
                                }

                                for (i in ngModelValue) {
                                    ngModelValue[i].$$id = lastId++;
                                }

                                $scope.value = ngModelValue;
                            }
                        }

                        if (!$scope.inEditor) {
                            parent[$scope.name] = $scope;
                        }
                    }, 0);
                    $timeout(function () {
                        $scope.loading = false;
                    }, 100);
                }
            }
        }
    };
});