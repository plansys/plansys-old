app.directive('relationField', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);

                // when ng-model is changed from inside directive
                $scope.renderFormList = function () {
                    $scope.renderedFormList = [];
                    for (key in $scope.formList) {
                        if (angular.isObject($scope.formList[key])) {
                            var subItem = [];
                            var rawSub = $scope.formList[key];

                            if (rawSub.hasOwnProperty('label')) {
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

                $scope.reload = function () {
                    $scope.doSearch(function () {
                        $("[relation-field] > data[rel_class='" + $scope.relClass + "']").each(function (idx, item) {
                            var itemScope = angular.element(item).scope();
                            if (!!itemScope && itemScope != $scope) {
                                itemScope.doSearch(function() {

                                });
                            }
                        });
                        $scope.updateInternal($scope.value, true);
                    });
                }

                $scope.unselect = function () {
                    $scope.value = '';
                    $scope.text = '';
                    $scope.renderedFormList.splice(0, $scope.renderedFormList.length);
                    $scope.doSearch();
                    ctrl.$setViewValue($scope.value);
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

                        if ($el.find("li.dropdown-item.hover").length == 0) {
                            $el.find("li.dropdown-item:eq(0)").addClass("hover");
                        }
                        
                        $a = $el.find("li.dropdown-item.hover").next();
                        
                        if ($a.length == 0) {
                            var ddParent = $el.find("li.dropdown-item.hover").parents("li.dropdown-header").next();
                            if (ddParent.length > 0) {
                                $a = ddParent.find("li.dropdown-item:eq(0)");
                            }
                        } else if ($a.hasClass("dropdown-header")) {
                            $a = $a.find("li.dropdown-item:eq(0)");            
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
                                $el.find("li.hover").removeClass("hover")
                                $a.addClass("hover");
                                
                                $timeout(function(){
                                    $el.find('.search-dropdown').focus();
                                }); 
                                
                            }
                        }
                        $scope.fixScroll();
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
                                if (ddParent.hasClass("dropdown-header")) {
                                    $a = ddParent.find("li.dropdown-item:last-child");            
                                } else {
                                    $a = ddParent;
                                }
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
                        if ($a.length > 0 && $a.is("li")) {
                            $el.find("li.hover").removeClass("hover")
                            $a.addClass("hover").find("a");
                            
                            $timeout(function(){
                                $el.find('.search-dropdown').focus();
                            });
                        }
                        $scope.fixScroll();
                        e.preventDefault();
                        e.stopPropagation();
                    }
                }

                $scope.update = function (item, f) {
                    $scope.updateInternal(item.key);
                };
                
                $scope.updateDetail = function(relation, func) {
                    if (!!$scope.value) {
                        $http.get(Yii.app.createUrl('formfield/RelationField.getDetail',{
                            'm': $scope.relModelClass,
                            'id': $scope.value,
                            'fd': $scope.idField
                        })).success(function (data) {
                            if (!!data) { 
                                if (angular.isObject($scope.model[relation])) {
                                    for (i in data) {
                                        $scope.model[relation][i] = data[i];
                                    }
                                } else {
                                    $scope.model[relation] = data;
                                }
                                
                                if (typeof func == "function") {
                                    func(data);
                                }
                            }
                        });
                    } else {
                        $scope.model[relation] = null;
                        if (typeof func == "function") {
                            func(null);
                        }
                    }
                }
    
                $scope.updateInternal = function (value, forceReload) {
                    function isEmpty(a) {
                        return !a || a == '';
                    }

                    $scope.value = typeof value != "string" && typeof value != 'number' ? '' : value.toString();

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
                    
                    if ((!isFound || !!forceReload) && typeof $el.find("li:eq(0) a").attr('value') != "undefined") {

                        // when current value not found in renderedFormList, then search it on server...
                        if (!!$scope.value) {
                            $scope.loading = true;
                            $http.post(Yii.app.createUrl('formfield/RelationField.findId'), {
                                's': '',
                                'm': $scope.modelClass,
                                'f': $scope.name,
                                'p': $scope.paramValue,
                                'i': $scope.identifier,
                                'v': $scope.value
                            }).success(function (data) {
                                $scope.loading = false;

                                if (data != "null") {
                                    var found = false;
                                    for (var key in $scope.renderedFormList) {
                                        var item = $scope.renderedFormList[key];
                                        if (item.value == data.label) {
                                            item.key = data.value;
                                            found = true;
                                        }
                                    }

                                    if (!found) {
                                        $scope.renderedFormList.push({
                                            key: data.value,
                                            value: data.label
                                        });
                                    }
                                    $scope.value = data.value;
                                    $scope.text = data.label;
                                    ctrl.$setViewValue($scope.value);

                                    if ($scope.identifier != '' && $scope.text) {
                                        parent.rel[$scope.identifier] = $scope.text.trim();
                                    }

                                } else {
//         WARNING: kalo di uncomment, infinite loop..
//                                    $scope.value = $el.find("li:eq(0) a").attr('value').trim();
//                                    $scope.text = $el.find("li:eq(0) a").text();
//                                    ctrl.$setViewValue($scope.value);
                                }
                            });
                        } else {
//         WARNING: kalo di uncomment, infinite loop..
//                            $scope.value = $el.find("li:eq(0) a").attr('value').trim();
//                            $scope.text = $el.find("li:eq(0) a").text();
//                            ctrl.$setViewValue($scope.value);
                        }
                    } else {
                        ctrl.$setViewValue($scope.value);
                    }

                    if ($scope.identifier != '' && $scope.text) {
                        parent.rel[$scope.identifier] = $scope.text.trim();
                    }


                    if ($scope.includeEmpty == 'Yes') {
                        if ((isEmpty($scope.value) && isEmpty($scope.text)) ||
                            $scope.value == $scope.emptyValue) {
                            $scope.value = $scope.emptyValue;
                            $scope.text = $scope.emptyLabel;
                        }
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

                $scope.doSearch = function (f) {
                    $scope.loading = true;
                    $http.post(Yii.app.createUrl('formfield/RelationField.search'), {
                        's': $scope.search,
                        'm': $scope.modelClass,
                        'f': $scope.name,
                        'p': $scope.paramValue,
                        'i': $scope.identifier,
                        'start': 0
                    }).success(function (data) {
                        $scope.formList = data.list;
                        $scope.count = data.count;
                        $scope.renderFormList();
                        $scope.loading = false;

                        if (typeof f == "function") {
                            f(data);
                        }
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
                
                $scope.reset = function() {
                    $scope.formList = null;
                    $scope.renderedFormList = null;
                    $scope.value = "";
                    $scope.text = "";
                    ctrl.$setViewValue('');
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
                            $scope.updateInternal(ctrl.$viewValue, true);
                        }
                    };
                }

                // set default value
                $scope.search = "";
                $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                $scope.params = JSON.parse($el.find("data[name=params]").text());
                $scope.renderedFormList = [];
                $scope.renderFormList();
                $scope.loading = true;
                $scope.count = $el.find("data[name=count]").html().trim();
                $scope.includeEmpty = $el.find("data[name=include_empty]").html().trim();
                $scope.emptyValue = $el.find("data[name=empty_value]").html().trim();
                $scope.emptyLabel = $el.find("data[name=empty_label]").html().trim();
                $scope.searchable = true;
                $scope.showOther = $el.find("data[name=show_other]").text().trim() == "Yes" ? true : false;
                $scope.showUnselect = $el.find("data[name=show_unselect]").text().trim() == "Yes" ? true : false;
                $scope.otherLabel = $el.find("data[name=other_label]").html();
                $scope.modelClass = $el.find("data[name=model_class]:eq(0)").html();
                $scope.relModelClass = $el.find("data[name=rel_model_class]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.relClass = $el.find("data[name=rel_class]:eq(0)").attr('rel_class').trim();
                $scope.modelField = JSON.parse($el.find("data[name=model_field]").text());
                $scope.paramValue = {};
                $scope.isOpen = false;
                $scope.disabledCondition = $el.find("data[name=is_disabled]").text().trim();
                $scope.identifier = $el.find("data[name=identifier]").text().trim();
                $scope.idField = $el.find("data[name=id_field]").text().trim();
                $scope.openedInField = false;
                $scope.jsParamsInitialized = true;

                $scope.isRelFieldDisabled = function() {
                    //console.log($scope.$parent.$eval($scope.disabledCondition));
                    return $scope.$parent.$eval($scope.disabledCondition);
                }
                
                $timeout(function () {
                    angular.forEach($scope.params, function (p, i) {
                        var p = $scope.params[i];
                        if (p.indexOf('js:') === 0) {
                            var value = $scope.$parent.$eval(p.replace('js:', ''));
                            var key = i;
                            var searchTimeout = null;
                            $scope.jsParamsInitialized = false;
                            $scope.paramValue[key] = value;

                            searchTimeout = $timeout(function () {
                                $scope.doSearch(function() {
                                    $scope.jsParamsInitialized = true;
                                });
                            }, 1000);

                            $scope.$watch(p.replace('js:', ''), function (newv, oldv) {
                                if (newv != oldv) {
                                    $scope.loading = true;
                                    if (searchTimeout) {
                                        $timeout.cancel(searchTimeout);
                                    }
                                    for (i in $scope.params) {
                                        var x = $scope.params[i];
                                        if (x == p) {
                                            $scope.paramValue[i] = newv;
                                        }
                                    }
                                    $scope.doSearch(function (data) {
                                        $scope.jsParamsInitialized = true;
//         WARNING: kalo di uncomment, infinite loop..
//                                        if (data.count == 0) {
//                                            $scope.value = '';
//                                            $scope.text = '';
//                                        } else {
//                                            $scope.value = data.list[0].value;
//                                            $scope.text = data.list[0].label;
//                                        }
                                    });
                                }
                            }, true);
                        }
                    });
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
                    $scope.loading = false;
                }, 100);

                parent[$scope.name] = $scope;
            }
        }
    };
});