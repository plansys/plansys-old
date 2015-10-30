app.directive('textField', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (!!ctrl) {
                        $timeout(function () {
                            ctrl.$setViewValue($scope.value);
                        }, 0);
                    }
                };

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.update();
                        }
                    };
                }

                $scope.renderFormList = function () {
                    $scope.list = [];
                    for (key in $scope.originalList) {
                        if (angular.isObject($scope.originalList[key])) {
                            var subItem = [];
                            var rawSub = $scope.originalList[key];

                            for (subkey in rawSub) {
                                subItem.push({key: subkey, value: rawSub[subkey]});
                            }
                            $scope.list.push({key: key, value: subItem});
                        } else {
                            $scope.list.push({key: key, value: $scope.originalList[key]});
                        }
                    }
                }

                // set default value
                var keytimeout = null;
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.relModelClass = $el.find("data[name=rel_model_class]").html();
                $scope.autocomplete = $el.find("data[name=autocomplete]").html();
                $scope.acMode = $el.find("data[name=ac_mode]").html();
                $scope.paramValue = {};
                $scope.showDropdown = false;
                if ($scope.autocomplete == 'php') {
                    $scope.originalList = JSON.parse($el.find("data[name=list]").text());
                    $scope.list = [];
                    $scope.renderFormList();
                } else {
                    $scope.list = [];
                }

                
                for (i in $scope.params) {
                    var p = $scope.params[i];
                    if (p != null && !!p.indexOf && p.indexOf('js:') === 0) {
                        var value = $scope.$parent.$eval(p.replace('js:', ''));
                        var key = i;

                        $scope.$parent.$watch(p.replace('js:', ''), function (newv, oldv) {
                            if (newv != oldv) {
                                for (i in $scope.params) {
                                    var x = $scope.params[i];
                                    if (x == p) {
                                        $scope.paramValue[i] = newv;
                                    }
                                }
                                $scope.doSearchRelation();
                            }
                        }, true);

                        $scope.paramValue[key] = value;
                        $scope.doSearchRelation();
                    }
                }

                $scope.doSearchList = function (val) {
                    $scope.search = val;
                    $scope.list = [];
                    for (i in $scope.originalList) {
                        var choice = $scope.originalList[i];
                        if (val == '' || $scope.isFound(choice)) {
                            $scope.list.push(choice);
                        }
                    }
                    $timeout(function () {
                        if (!$scope.initSearch) {
                            $scope.initSearch = true;
                        } else {
                            $scope.openDropdown(true);
                        }
                    });
                }

                $scope.isFound = function (input) {
                    return $scope.search == '' || input.toLowerCase().indexOf($scope.search.toLowerCase()) > -1;
                }

                $scope.doSearchRelation = function (val) {
                    $http.post(Yii.app.createUrl('formfield/RelationField.search'), {
                        's': val,
                        'm': $scope.modelClass,
                        'f': $scope.name,
                        'p': $scope.paramValue
                    }).success(function (data) {
                        $scope.list = [];

                        for (l in data.list) {
                            if (data.list[l] != null && !!data.list[l].value) {
                                $scope.list.push(data.list[l]);
                            }
                        }

                        if (!$scope.initSearch) {
                            $scope.initSearch = true;
                        } else {
                            $scope.openDropdown(true);
                        }
                    });
                }
                $scope.closeDropdown = function () {
                    $el.find('[dropdown]').removeClass('open');
                    $scope.showDropDown = false;
                }
                $scope.openDropdown = function (scroll) {
                    $timeout(function () {
                        if (!$el.find("input[type=text]").is(':focus'))
                            return;

                        var isOpened = $scope.showDropDown == true;
                        if (!$scope.blurred && $scope.list && $scope.list.length > 0 && (!isOpened || scroll)) {
                            $el.find('[dropdown]').addClass('open');
                            $scope.showDropDown = true;

                            if (scroll) {
                                $el.find('.dropdown-menu').scrollTop(0);
                                $el.find('.dropdown-menu li.hover').removeClass('hover');
                                $timeout(function () {
                                    var f = $el.find('.dropdown-menu li:eq(0)');
                                    f.addClass('hover');
                                });
                            }
                        }
                    });
                }

                $scope.choose = function (choice) {
                    $scope.dropdownHover = true;
                    var val = choice || $el.find(".dropdown-menu li.hover a").text();
                    switch ($scope.acMode) {
                        case "comma":
                            var vr = $scope.value ? $scope.value.split(",") : [''];
                            if (vr.length > 1) {
                                vr[vr.length - 1] = val;
                            } else {
                                vr[0] = val;
                            }
                            for (i in vr) {
                                vr[i] = vr[i].trim();
                            }
                            $scope.value = vr.join(", ");
                            $scope.search = val;
                            break;
                        default:
                            $scope.value = val;
                            break;
                    }
                    $scope.closeDropdown();
                    $el.find("input[type=text]").focus();

                    $timeout(function () {
                        ctrl.$setViewValue($scope.value);
                        $scope.dropdownHover = false;
                    });
                }

                $scope.doSearch = function (val) {
                    $scope.blurred = false;

                    clearTimeout(keytimeout);
                    keytimeout = setTimeout(function () {
                        val = val || $scope.value;
                        switch ($scope.acMode) {
                            case "comma":
                                val = typeof val == "string" ? val.split(",").pop() : '';
                            default:
                                break;
                        }

                        switch ($scope.autocomplete) {
                            case "rel":
                                $scope.doSearchRelation(val);
                                break
                            case "php":
                                $scope.doSearchList(val);
                                break;
                        }
                    }, 100);
                }


                if ($scope.autocomplete != '') {
                    $el.find("[dropdown]").hover(function (e) {
                        $scope.dropdownHover = true;
                    }, function (e) {
                        $scope.dropdownHover = false;
                    });

                    if ($scope.autocomplete) {
                        $timeout(function () {
                            $scope.doSearch('');
                        });
                    }
                    $el.find("input[type=text]").focus(function (e) {
                        if (!$scope.dropdownHover) {
                            $scope.openDropdown($scope.value ? false : true);
                        }
                        e.preventDefault();
                    });

                    $el.find("input[type=text]").blur(function (e) {
                        if (!$scope.dropdownHover) {
                            $scope.closeDropdown();
                        }
                        e.preventDefault();
                    });
                    $el.find("input[type=text]").keydown(function (e) {
                        if ($scope.autocomplete == '')
                            return true;

                        switch (e.keyCode) {
                            case 37:
                            case 39:
                                $scope.dropdownHover = false;
                                $timeout(function () {
                                    $scope.openDropdown();
                                });
                                break;
                            case 9:
                                $scope.closeDropdown();
                                break;
                            case 13:
                                $scope.choose();
                                e.preventDefault();
                                e.stopPropagation();
                                return true;
                            case 188:
                                if ($scope.acMode == 'comma') {
                                    if ($scope.showDropdown) {
                                        $timeout(function () {
                                            $scope.choose();
                                        });
                                    } else {
                                        $scope.doSearch(e);
                                    }
                                }
                                break;
                            case 38:
                                if (!$el.find('[dropdown]').hasClass('open')) {
                                    $scope.dropdownHover = false;
                                    $timeout(function () {
                                        $scope.openDropdown();
                                    });
                                    return false;
                                }

                                $scope.dropdownHover = true;
                                $a = $el.find(".dropdown-menu li.hover").prev();
                                if ($a.length && $a.length == 0) {
                                    $a = $el.find("li:last-child");
                                }

                                var i = 0;
                                while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                                    $a = $a.prev();
                                    i++;
                                }
                                if ($a.length && $a.length > 0 && $a.is("li")) {
                                    $el.find(".dropdown-menu li.hover").removeClass("hover")
                                    $a.addClass("hover").find("a").focus();
                                }

                                $timeout(function () {
                                    $el.find("input[type=text]").focus();
                                    $scope.dropdownHover = false;
                                });

                                e.preventDefault();
                                e.stopPropagation();
                                break;
                            case 40:
                                if (!$el.find('[dropdown]').hasClass('open')) {
                                    $scope.dropdownHover = false;
                                    $timeout(function () {
                                        $scope.openDropdown();
                                    });
                                    return false;
                                }

                                $scope.dropdownHover = true;
                                $a = $el.find(".dropdown-menu li.hover").next();
                                if ($a.length && $a.length == 0 && $scope.list.length > 0) {
                                    $scope.updateInternal($scope.list[0].key);
                                } else {
                                    var i = 0;
                                    while ((!$a.is("li") || !$a.is(":visible")) && i < 100) {
                                        $a = $a.next();
                                        i++;
                                    }

                                    if ($a.length && $a.length > 0 && $a.is("li")) {
                                        $el.find(".dropdown-menu li.hover").removeClass("hover");
                                        $a.addClass("hover").find("a").focus();
                                    }
                                }

                                $timeout(function () {
                                    $el.find("input[type=text]").focus();
                                    $scope.dropdownHover = false;
                                });
                                e.preventDefault();
                                e.stopPropagation();
                                break;
                            default:
                                $scope.doSearch();
                                break;
                        }
                    });
                }

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                    }, 0);
                }

            }
        }
    };
});