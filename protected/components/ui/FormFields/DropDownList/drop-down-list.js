app.directive('dropDownList', function($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.renderFormList = function() {
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

                $scope.dropdownKeypress = function(e) {
                    if (e.which === 13) {
                        $timeout(function() {
                            $el.find("li.hover a").click();
                            $scope.isOpen = false;
                        }, 0);

                        e.preventDefault();
                        e.stopPropagation();
                    }
                    if (e.which === 40) {
                        $scope.isOpen = true;

                        $a = $el.find("li.hover").next();
                        var i = 0;
                        while (!$a.is(":visible") && i < 100) {
                            $a = $a.next();
                            i++;
                        }

                        if ($a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover");
                        }
                        e.preventDefault();
                        e.stopPropagation();
                    } else if (e.which === 38) {
                        $scope.isOpen = true;

                        $a = $el.find("li.hover").prev();
                        var i = 0;
                        while (!$a.is(":visible") && i < 100) {
                            $a = $a.prev();
                            i++;
                        }
                        if ($a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover");
                        }
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }

                $scope.update = function(value, f) {
                    $scope.updateInternal(value);
                    $timeout(function() {
                        ctrl.$setViewValue($scope.value);
                        if ($scope.showOther && !$scope.itemExist()) {
                            $el.find('.dropdown-other-type').focus();
                        } else {
                            $el.focus();
                        }
                    }, 0);
                };
                $scope.updateInternal = function(value) {
                    $scope.value = value;
                    if ($scope.showOther && !$scope.itemExist()) {
                        $scope.value = $el.find("li a").attr('value');
                        $scope.value = value;
                    }

                    $scope.text = $el.find("li a[value='" + value + "']").html();
                    $scope.toggled(false);
                };

                $scope.updateOther = function(value) {
                    $scope.showOther = false;
                    $scope.updateInternal(value);
                    ctrl.$setViewValue($scope.value);
                    $scope.showOther = true;
                }

                $scope.itemExist = function(value) {
                    if (typeof value == "undefined")
                        value = $scope.value;

                    return $el.find("li.dropdown-item a[value='" + value + "']").length != 0;
                }

                $scope.toggled = function(open) {
                    if (open) {
                        if ($el.find("li a[value='" + $scope.value + "']").length > 0) {
                            $el.find("li.hover").removeClass("hover")
                            $el.find("li a[value='" + $scope.value + "']").parent().addClass('hover');

                            if ($scope.searchable) {
                                $timeout(function() {
                                    $el.find('.search-dropdown').focus();
                                }, 0);
                            }
                        } else {
                            $el.find("li a").blur();
                            $el.find(".dropdown-menu").scrollTop(0);
                        }
                        $el.focus();
                    }
                };
                $scope.changeOther = function() {
                    $scope.value = $scope.otherLabel;
                };
                $scope.doSearch = function() {
                    $timeout(function() {
                        $el.find("li.hover").removeClass("hover");
                        $el.find("li:not(.ng-hide):first").addClass("hover");
                    }, 0);
                };

                $scope.isObject = function(input) {
                    return angular.isObject(input);
                }

                $scope.isFound = function(input) {
                    return $scope.search == '' || input.toLowerCase().indexOf($scope.search.toLowerCase()) > -1;
                }

                // when ng-model, or ps-list is changed from outside directive
                if (attrs.psList) {
                    function changeFieldList() {
                        $timeout(function() {
                            $scope.formList = $scope.$eval(attrs.psList);
                            $scope.renderFormList();
                            $scope.updateInternal($scope.value);
                        }, 0);
                    }
                    $scope.$watch(attrs.psList, changeFieldList);
                }

                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function() {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.updateInternal(ctrl.$viewValue);
                        }
                    };
                }

                // set default value
                $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                $scope.renderedFormList = [];
                $scope.renderFormList();

                $scope.searchable = $el.find("data[name=searchable]").text().trim() == "Yes" ? true : false;
                $scope.showOther = $el.find("data[name=show_other]").text().trim() == "Yes" ? true : false;
                $scope.otherLabel = $el.find("data[name=other_label]").html();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";
                $scope.isOpen = false;

                $scope.search = "";
                //if ngModel is present, use that instead of value from php
                $timeout(function() {
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