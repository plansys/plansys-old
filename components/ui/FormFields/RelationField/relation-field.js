
app.directive('relationField', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.$parent;

                // when ng-model is changed from inside directive
                $scope.renderFormList = function () {
                    $scope.renderedFormList = [];
                    for (key in $scope.formList) {
                        if (angular.isObject($scope.formList[key])) {
                            var subItem = [];
                            var rawSub = $scope.formList[key];

                            if (rawSub.label) {
                                $scope.renderedFormList.push({
                                    key: rawSub.value,
                                    value: rawSub.label
                                });
                            } else {
                                for (subkey in rawSub) {
                                    subItem.push({key: subkey, value: rawSub[subkey]});
                                }
                                $scope.renderedFormList.push({key: key, value: subItem});
                            }
                        } else {
                            $scope.renderedFormList.push({key: key, value: $scope.formList[key]});
                        }
                    }
                }

                $scope.fixScroll = function () {
                    $el.find(".dropdown-menu").scrollTop(0);
                    var top = $el.find("li:eq(0)").offset().top;
                    var scroll = $el.find("li a[value='" + $scope.value + "']").offset().top;
                    $el.find(".dropdown-menu").scrollTop(scroll - top);
                }

                $scope.dropdownKeypress = function (e) {
                    if (e.which === 13) {
                        if ($scope.isOpen) {
                            $timeout(function () {
                                $el.find("li.hover a").click();
                                $scope.isOpen = false;
                            }, 0);
                        } else {
                            $timeout(function () {
                                $scope.isOpen = true;
                            }, 0);
                        }

                        e.preventDefault();
                        e.stopPropagation();
                    }
                    if (e.which === 40) {
                        $scope.isOpen = true;

                        $a = $el.find("li.hover").next();
                        if ($a.length == 0 && $scope.renderedFormList.length > 0) {
                            $scope.updateInternal($scope.renderedFormList[0].key);
                            $el.find("li:eq(1)").addClass("hover");
                        } else {
                            var i = 0;
                            while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                                $a = $a.next();
                                i++;
                            }

                            if ($a.length > 0 && $a.is("li")) {
                                $el.find("li.hover").removeClass("hover")
                                $a.addClass("hover");
                            }
                        }
                        $scope.fixScroll();
                        e.preventDefault();
                        e.stopPropagation();
                    } else if (e.which === 38) {
                        $scope.isOpen = true;

                        $a = $el.find("li.hover").prev();
                        var i = 0;
                        while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                            $a = $a.prev();
                            i++;
                        }
                        if ($a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover").find("a");
                        }
                        $scope.fixScroll();
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }

                $scope.update = function (value, f) {
                    $scope.updateInternal(value);
                    $timeout(function () {
                        ctrl.$setViewValue($scope.value);
                    }, 0);
                };
                $scope.updateInternal = function (value) {
                    $scope.value = typeof value != "string" ? '' : value;

                    if ($scope.showOther && !$scope.itemExist()) {
                        $scope.value = $el.find("li a").attr('value');
                        $scope.value = value;
                    }

                    var isFound = false;
                    $el.find("li").each(function () {
                        var fv = $(this).find("a").attr('value');
                        if (!!fv && fv.trim() == $scope.value.trim()) {
                            $scope.text = $(this).find("a").text();
                            isFound = true;
                        }
                    });

                    if (!isFound && $el.find("li:eq(0) a").attr('value')) {
                        $scope.value = $el.find("li:eq(0) a").attr('value').trim();
                        $scope.text = $el.find("li:eq(0) a").text();
                        ctrl.$setViewValue($scope.value);
                    }

                    if ($scope.identifier != '' && $scope.text) {
                        parent.rel[$scope.identifier] = $scope.text.trim();
                    }

                    $scope.toggled(false);
                };

                $scope.updateOther = function (value) {
                    $scope.showOther = false;
                    $scope.updateInternal(value);
                    ctrl.$setViewValue($scope.value);
                    $scope.showOther = true;
                }

                $scope.itemExist = function (value) {
                    if (typeof value == "undefined")
                        value = $scope.value;

                    return $el.find("li.dropdown-item a[value='" + value + "']").length != 0;
                }

                $scope.searchFocus = function (e) {
                    e.stopPropagation();
                    var watch = $scope.$watch('isOpen', function (n) {
                        if (n === false) {
                            $scope.isOpen = true;
                            watch();
                            $timeout(function () {
                                $el.find('.search-dropdown').focus();
                            }, 0);
                        }
                    }, true);
                }

                $scope.toggled = function (open) {
                    if (open) {
                        $scope.openedInField = true;
                        if ($el.find("li a[value='" + $scope.value + "']").length > 0) {
                            $el.find("li.hover").removeClass("hover")
                            $el.find("li a[value='" + $scope.value + "']").parent().addClass('hover');

                            $scope.fixScroll();
                        } else {
                            $el.find("li a").blur();
                            $el.find(".dropdown-menu").scrollTop(0);
                        }

                        if ($scope.searchable) {
                            $el.find(".search-dropdown").focus();
                        }
                    } else if ($scope.openedInField) {
                        $scope.openedInField = true;
                        $el.find("[dropdown] button").focus();
                    }
                };

                $scope.changeOther = function () {
                    $scope.value = $scope.otherLabel;
                };

                $scope.doSearch = function () {
                    $scope.loading = true;
                    $http.post(Yii.app.createUrl('formfield/RelationField.search'), {
                        's': $scope.search,
                        'm': $scope.modelClass,
                        'f': $scope.name,
                        'p': $scope.paramValue,
                        'i': $scope.identifier
                    }).success(function (data) {
                        $scope.formList = data.list;
                        $scope.count = data.count;
                        $scope.renderFormList();
                        $scope.loading = false;
                    });
                };
                $scope.next = function (e) {
                    e.stopPropagation();
                    e.preventDefault();

                    $scope.loading = true;
                    $http.post(Yii.app.createUrl('formfield/RelationField.search'), {
                        's': $scope.search,
                        'm': $scope.modelClass,
                        'f': $scope.name,
                        'p': $scope.paramValue,
                        'i': $scope.identifier,
                        'start': $scope.formList.length
                    }).success(function (data) {
                        data.list.forEach(function (item) {
                            $scope.formList.push(item);
                        });
                        $scope.renderFormList();
                        $scope.loading = false;
                    });
                };

                $scope.isObject = function (input) {
                    return angular.isObject(input);
                }

                // when ng-model, or ps-list is changed from outside directive
                if (attrs.psList) {
                    function changeFieldList() {
                        $timeout(function () {
                            $scope.formList = $scope.$eval(attrs.psList);
                        }, 0);
                    }
                    $scope.$watch(attrs.psList, changeFieldList);
                }

                // watch form list
                $scope.$watch('formList', function (n, o) {
                    $timeout(function () {
                        $scope.renderFormList();
                        $scope.openedInField = false;
                        $scope.updateInternal($scope.value);
                    });
                }, true);

                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.updateInternal(ctrl.$viewValue);
                        }
                    };
                }

                // set default value
                $scope.search = "";
                $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                $scope.params = JSON.parse($el.find("data[name=params]").text());
                $scope.renderedFormList = [];
                $scope.renderFormList();
                $scope.loading = false;
                $scope.count = $el.find("data[name=count]").html().trim();
                $scope.searchable = true;
                $scope.showOther = $el.find("data[name=show_other]").text().trim() == "Yes" ? true : false;
                $scope.otherLabel = $el.find("data[name=other_label]").html();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.name = $el.find("data[name=name]").html().trim();
                $scope.modelField = JSON.parse($el.find("data[name=model_field]").text());
                $scope.paramValue = {};
                $scope.isOpen = false;
                $scope.identifier = $el.find("data[name=identifier]").text().trim();
                $scope.openedInField = false;

                angular.forEach($scope.params, function (p, i) {
                    var p = $scope.params[i];
                    if (p.indexOf('js:') === 0) {
                        var value = $scope.$eval(p.replace('js:', ''));
                        var key = i;

                        $scope.$watch(p.replace('js:', ''), function (newv, oldv) {
                            if (newv != oldv) {
                                for (i in $scope.params) {
                                    var x = $scope.params[i];
                                    if (x == p) {
                                        $scope.paramValue[i] = newv;
                                    }
                                }
                                $scope.doSearch();
                            }
                        }, true);

                        $scope.paramValue[key] = value;
//                        $scope.doSearch()
                    }
                });

                //if ngModel is present, use that instead of value from php
                $timeout(function () {
                    if (attrs.ngModel) {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.updateInternal(ngModelValue);
                        } else {
                            $scope.updateInternal($el.find("data[name=value]").html());
                        }
                    } else {
                        $scope.updateInternal($el.find("data[name=value]").html());
                    }

                    if (attrs.searchable) {
                        $scope.searchable = $scope.$parent.$eval(attrs.searchable);
                    }
                }, 100);
            }
        }
    };
});