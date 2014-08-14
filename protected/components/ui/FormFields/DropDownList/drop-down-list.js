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
                $scope.update = function(value, f) {
                    $scope.updateInternal(value);
                    $timeout(function() {
                        ctrl.$setViewValue($scope.value);
                        if ($scope.showOther && !$scope.itemExist()) {
                            $el.find('.dropdown-other-type').focus();
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
                    $timeout(function() {
                        ctrl.$setViewValue($scope.value);
                        $scope.showOther = true;
                    }, 0);
                }

                $scope.itemExist = function(value) {
                    if (typeof value == "undefined")
                        value = $scope.value;

                    return $el.find("li.dropdown-item a[value='" + value + "']").length != 0;
                }

                $scope.toggled = function(open) {
                    if (open) {
                        if ($el.find("li a[value='" + $scope.value + "']").length > 0) {
                            $el.find("li a[value='" + $scope.value + "']").focus();

                            if ($scope.searchable) {
                                $timeout(function() {
                                    $el.find('.search-dropdown').focus();
                                    $el.find("li.hover").removeClass("hover");
                                    $el.find("li a[value='" + $scope.value + "']").parent().addClass("hover");
                                }, 0);
                            }
                        } else {
                            $el.find("li a").blur();
                            $el.find(".dropdown-menu").scrollTop(0);
                        }
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
                $el.find(".search-dropdown").keydown(function(e) {
                    if (e.which === 13) {
                        $el.find("li.hover a").click();
                    }
                    if (e.which === 40) {
                        $a = $el.find("li.hover").next();
                        if ($a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover");
                        }
                    } else if (e.which === 38) {
                        $a = $el.find("li.hover").prev();
                        if ($a.length > 0) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover");
                        }
                    }
                });

                // when ng-model, or ng-form-list is changed from outside directive

                if (attrs.ngFormList) {
                    function changeFieldList() {
                        $timeout(function() {
                            $scope.formList = $scope.$eval(attrs.ngFormList);
                            $scope.updateInternal($scope.value);
                        }, 0);
                    }
                    $scope.$watch(attrs.ngFormList, changeFieldList);
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
                $scope.searchable = $el.find("data[name=searchable]").text().trim() == "Yes" ? true : false;
                $scope.showOther = $el.find("data[name=show_other]").text().trim() == "Yes" ? true : false;
                $scope.otherLabel = $el.find("data[name=other_label]").html();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";
                $scope.search = "";
                //if ngModel is present, use that instead of value from php
                $timeout(function() {
                    if (attrs.ngModel) {
                        var ngModelValue = $scope.$parent.$eval(attrs.ngModel);
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