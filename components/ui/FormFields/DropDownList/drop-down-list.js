app.directive('dropDownList', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.lastFocus = 'search';
                // when ng-model is changed from inside directive
                $scope.renderFormList = function () {
                    $scope.renderedFormList = [];
                    for (key in $scope.formList) {
                        if (angular.isObject($scope.formList[key])) {
                            var subItem = [];
                            var rawSub = $scope.formList[key];

                            for (subkey in rawSub) {
                                subItem.push({key: subkey, value: rawSub[subkey]});
                            }
                            $scope.renderedFormList.push({key: key, value: subItem});
                        } else {
                            $scope.renderedFormList.push({key: key, value: $scope.formList[key]});
                        }
                    }
                }

                $scope.dropdownKeypress = function (e) {
                    if (e.which === 13) {
                        if ($scope.isOpen) {
                            $timeout(function () {
                                $el.find("li.dropdown-item.hover a:eq(0)").click();
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

                        if ($el.find("li.dropdown-item.hover").length == 0) {
                            $el.find("li.dropdown-item:eq(0)").addClass("hover");
                        }
                        
                        $a = $el.find("li.dropdown-item.hover").next();
                        
                        if ($a.length == 0) {
                            var ddParent = $el.find("li.dropdown-item.hover").parents("li.dropdown-header").next();
                            if (ddParent.length > 0) {
                                $a = ddParent.find("li.dropdown-item:eq(0)");
                            }
                        }
                        
                        if ($a.length == 0 && $scope.renderedFormList.length > 0) {
                            $scope.updateInternal($scope.renderedFormList[0].key);
                        } else {
                            var i = 0;
                            while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                                $a = $a.next();
                                i++;
                            }

                            if ($a.length > 0 && $a.is("li")) {
                                $el.find("li.hover").removeClass("hover");
                                $a.addClass("hover").find("a:eq(0)").focus();
                            }
                        }
                        e.preventDefault();
                        e.stopPropagation();
                    } else if (e.which === 38) {
                        $scope.isOpen = true;

                        if ($el.find("li.dropdown-item.hover").length == 0) {
                            $el.find("li.dropdown-item:eq(0)").addClass("hover");
                        }
                        
                        $a = $el.find("li.dropdown-item.hover").prev();

                        if ($a.length == 0) {
                            var ddParent = $el.find("li.dropdown-item.hover").parents("li.dropdown-header").prev();
                            if (ddParent.length > 0) {
                                $a = ddParent.find("li.dropdown-item:last-child");
                            }
                        }

                        if ($a.length && $a.length == 0) {
                            $a = $el.find("li:last-child");
                        }

                        var i = 0;
                        while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                            $a = $a.prev();
                            i++;
                        }
                        if ($a.length && $a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover").find("a:eq(0)").focus();
                        }
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }

                $scope.update = function (value) {
                    $scope.updateInternal(value);
                    $timeout(function () {
                        ctrl.$setViewValue($scope.value);
                        $scope.lastFocus = 'search';
                    }, 0);
                };

                $scope.updateInternal = function (value) {
                    $scope.value = ['number', 'string'].indexOf(typeof value) < 0 ? '' : value + '';

                    var isFound = false;
                    $el.find("li").each(function () {
                        if (typeof ($(this).find("a").attr('value')) != "undefined" && $(this).find("a").attr('value').trim() == $scope.value.trim()) {
                            $scope.text = $(this).find("a").text();
                            isFound = true;
                        }
                    });

                    if (!$scope.showOther && !isFound && $el.find("li:eq(0) a").attr('value')) {
                        $scope.value = $el.find("li:eq(0) a").attr('value').trim();
                        $scope.text = $el.find("li:eq(0) a").text();
                        $timeout(function () {
                            $el.find('.dropdown-text').html($scope.text);
                        }, 1000);
                    }

                    $timeout(function () {
                        if ($scope.showOther && !isFound && !$scope.formList[value]) {
                            $scope.valueOther = value;
                            $scope.text = value;
                            $el.find("li.hover").removeClass("hover");
                            $el.find("li.dropdown-other").addClass("hover");
                        }
                    }, 100);

                    $scope.toggled(false);
                };

                $scope.trimText = function (text) {
                    return text.trim();
                }

                $scope.selectOther = function () {
                    if ($scope.valueOther != '') {
                        $scope.value = $scope.valueOther;
                        $scope.text = $scope.valueOther;
                        $el.find("li.hover").removeClass("hover");
                        $el.find("li.dropdown-other").addClass("hover");
                    }
                }

                $scope.updateOther = function (value) {
                    $scope.valueOther = value;
                    $scope.value = value;
                    $scope.text = value;
                    ctrl.$setViewValue($scope.value);
                    $timeout(function () {
                        if ($scope.itemExist()) {
                            $scope.valueOther = '';
                            $scope.updateInternal($scope.value);

                            $el.find("li.hover").removeClass("hover")
                            $el.find("li a[value='" + $scope.value + "']").focus().parent().addClass('hover');
                        } else {
                            $el.find("li.hover").removeClass("hover");
                            $el.find("li.dropdown-other").addClass("hover");
                        }

                        $scope.text = $scope.value;
                        if ($scope.valueOther != $scope.value) {
                            $scope.valueOther = $scope.value;
                            $el.find(".dropdown-other-type").val($scope.value);
                        }

                        if ($scope.valueOther == '') {
                            $el.find(".dropdown-other-type").focus();
                        }
                    });
                }

                $scope.itemExist = function (value, text) {
                    if (!value || value.trim() == '')
                        value = $scope.value;

                    if (!text || text.trim() == '')
                        text = $scope.text;

                    if (!value)
                        return true;

                    var valueExist = $el.find("li.dropdown-item a[value='" + value + "']").length != 0;
                    var textExist = $el.find("li.dropdown-item a:contains('" + text + "')").length != 0;

                    return valueExist || textExist;
                }

                $scope.textFieldFocus = function (e, lastFocus) {
                    e.stopPropagation();
                    $scope.lastFocus = lastFocus;
                    var watch = $scope.$watch('isOpen', function (n) {
                        if (n === false) {
                            $scope.isOpen = true;
                            watch();
                        }
                    }, true);
                }

                $scope.toggled = function (open) {
                    if (open) {
                        $scope.openedInField = true;
                        if ($el.find("li a[value='" + $scope.value + "']").length > 0) {
                            $el.find("li.hover").removeClass("hover")
                            $el.find("li a[value='" + $scope.value + "']").focus().parent().addClass('hover');

                        } else {
                            $el.find("li a").blur();
                            $el.find(".dropdown-menu").scrollTop(0);
                        }
                        if ($scope.searchable && $scope.lastFocus == 'search') {
                            $timeout(function () {
                                $el.find('.search-dropdown').focus();
                            }, 0);
                        }
                        if ($scope.lastFocus == 'other') {
                            $timeout(function () {
                                $el.find('.dropdown-other-type').focus();

                                if (!$scope.itemExist()) {
                                    $el.find("li.hover").removeClass("hover");
                                    $el.find("li.dropdown-other").addClass("hover");
                                }
                            }, 0);
                        }
                    } else if ($scope.openedInField) {
                        $scope.openedInField = true;
                        if ($scope.showOther && !$scope.itemExist()) {
                            $el.find(".dropdown-other-type").focus();
                        } else {
                            $el.find("[dropdown] button").focus();
                        }
                    }
                };

                $scope.changeOther = function () {
                    $scope.value = $scope.otherLabel;
                };

                $scope.doSearch = function () {
                    $timeout(function () {
                        $el.find("li.hover").removeClass("hover");
                        $el.find("li:not(.ng-hide):first").addClass("hover");
                    }, 0);
                };

                $scope.isObject = function (input) {
                    return angular.isObject(input);
                }

                $scope.isFound = function (input) {
                    if (!$scope.search)
                        return true;

                    return $scope.search == '' || input.toLowerCase().indexOf($scope.search.toLowerCase()) > -1;
                }

                // when ng-model, or ps-list is changed from outside directive
                if (attrs.psList) {
                    function changeFieldList() {
                        $scope.formList = $scope.$eval(attrs.psList);
                        $scope.renderFormList();

                        $timeout(function () {
                            $scope.updateInternal($scope.value);
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

                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;
                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.updateInternal(ctrl.$viewValue);
                        }
                    };
                }

                // set default value
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                $scope.renderedFormList = [];
                $scope.renderFormList();

                $scope.valueOther = '';
                $scope.searchable = $el.find("data[name=searchable]").text().trim() == "Yes" ? true : false;
                $scope.showOther = $el.find("data[name=show_other]").text().trim() == "Yes" ? true : false;
                $scope.otherLabel = $el.find("data[name=other_label]").html();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.defaultValue = $el.find("data[name=default_value]").html().trim();
                $scope.defaultType = $el.find("data[name=default_type]").html().trim();
                $scope.isOpen = false;
                $scope.text = "";
                $scope.openedInField = false;

                $scope.search = "";
                $timeout(function () {
                    var initialValue = $el.find("data[name=value]").html();
                    if (attrs.ngModel) {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            initialValue = ngModelValue;
                        }
                    }
                    if ($scope.defaultType == 'first' && !initialValue && !!$scope.renderedFormList[0].value) {
                        initialValue = $scope.renderedFormList[0].value;
                    }

                    if (!!initialValue) {
                        $scope.updateInternal(initialValue);
                    }

                    if ($scope.defaultType == 'first') {
                        ctrl.$setViewValue(initialValue);
                    }

                    $timeout(function () {
                        if (typeof ctrl.$viewValue == 'undefined') {
                            if (!$scope.formList['']) {
                                $scope.text = '';
                            } else if (!!$scope.formList['']) {
                                $scope.text = $scope.formList[''];
                            }
                        }
                    });
                    if (attrs.searchable) {
                        $scope.searchable = $scope.$parent.$eval(attrs.searchable);
                    }
                });
            }
        }
    };
});