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

                // when ng-model is changed from inside directive

                $scope.updateListView = function () {
                    if (typeof ctrl != 'undefined') {
                        $scope.itemChanging = true;
                        ctrl.$setViewValue(angular.copy($scope.value));
                        $timeout(function () {
                            $scope.itemChanging = false;
                        }, 1000);
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
                }

                $scope.removeItem = function (index) {
                    var d = $scope.value.splice(index, 1);
                    $scope.deleted = {
                        idx: index,
                        data: d[0]
                    };
                    $scope.updateListView();
                    $scope.showUndoDelete = true;
                }

                $scope.addItem = function (e) {

                    e.preventDefault();
                    e.stopPropagation();
                    if ($scope.value == null || typeof $scope.value == "undefined" || $scope.value == "") {
                        $scope.value = [];
                    }

                    if ($scope.fieldTemplate == "default") {
                        var value = '';
                    } else if ($scope.fieldTemplate == "form") {
                        var value = angular.extend({}, $scope.templateAttr);
                    }

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
                            $el.find('.list-view-item-text').last().focus();
                        }, 0);
                    }, 0);
                }


                $scope.uiTreeOptions = {
                    dragStop: function (scope) {
                        $scope.updateListView();
                    }
                };

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.loading = true;
                            $scope.value = ctrl.$viewValue;
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

                // set default value
                $scope.value = JSON.parse($el.find("data[name=value]").html().trim());
                $scope.modelClass = $el.find("data[name=model_class]").html().trim();
                $scope.fieldTemplate = $el.find("data[name=field_template]").html().trim();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.templateAttr = JSON.parse($el.find("data[name=template_attr]").html().trim());
                $scope.options = JSON.parse($el.find("data[name=options]").html().trim());

                // change Watchers
                $timeout(function () {
                    if (!!$scope.options['ng-change']) {
                        $scope.stopWatchChanges = $scope.$watch('value', function (n, o) {
                            if (n !== o) {
                                if ($scope.itemChanging) {
                                    ctrl.$viewValue = $scope.value;
                                } else {
                                    ctrl.$setViewValue(angular.copy($scope.value));
                                }
                            }
                        }, true);
                    }
                });

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                        if (!$scope.inEditor) {
                            parent[$scope.name] = $scope;
                        }
                    }, 0);
                }
            }
        }
    };
});