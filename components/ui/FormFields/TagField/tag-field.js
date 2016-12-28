app.directive('tagField', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var input, insig, hors;

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

                // define vars
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.dropdown = $el.find("data[name=dropdown]").html().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.renderID = $el.find("data[name=render_id]").html();
                $scope.mustChoose = $el.find("data[name=must_choose]").html();
                $scope.params = $el.find("data[params]").html();
                $scope.valueArray = [];
                $scope.delimiter = ',';
                $scope.unique = 'yes';
                $scope.tfLoaded = false;
                $scope.loading = false;
                $scope.relCount = 0;

                if ($scope.dropdown == 'normal') {
                    $scope.originalList = JSON.parse($el.find("data[name=list]").text());
                    $scope.list = [];
                    $scope.renderFormList();
                } else {
                    $scope.list = [];
                }

                // define current form field in parent scope
                $scope.parent = $scope.getParent($scope);
                $scope.parent[$scope.name] = $scope;

                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (!!ctrl) {
                        if (typeof $scope.value == 'string') {
                            $scope.valueArray = $scope.value.split($scope.delimiter);
                        } else if (typeof $scope.value == 'object') {
                            $scope.valueArray = $scope.value;
                            $scope.value = $scope.value.join($scope.delimiter);
                        }

                        ctrl.$setViewValue($scope.value);

                        if (!!insig) {
                            insig.destroy();
                            $scope.tfLoaded = false;
                            $(input).val($scope.value);
                            $scope.init();
                        }
                    }
                };

                $scope.splitValues = function () {
                    if (!!insig) {
                        $scope.valueArray = insig.value();
                    }
                }

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.splitValues();
                        }
                    };
                }

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                            $scope.splitValues();
                        }
                    });
                }

                $scope.formatList = function () {
                    var list = [];
                    for (var i in $scope.list) {
                        list.push($scope.list[i].value);
                    }
                    return list;
                }

                $scope.formatDropdownList = function () {
                    var list = [];
                    var exist = insig.value();
                    for (var i in $scope.list) {
                        if ($scope.unique != 'yes' || ($scope.unique == 'yes' && exist.indexOf($scope.list[i].value) < 0)) {
                            list.push($scope.list[i].value);
                        }
                    }
                    return list;
                }

                $scope.refreshDropdownList = function () {
                    if (hors.add) {
                        hors.clear();
                        var list = $scope.formatDropdownList();
                        for (i in list) {
                            hors.add(list[i]);
                        }
                    }
                }

                $scope.init = function () {
                    insig = insignia(input,{
                        delimiter: $scope.delimiter,
                        preventInvalid: true,
                        validate: function (value) {
                            var valid = true;
                            if ($scope.dropdown == 'rel' || 
                                ($scope.dropdown == 'normal' && $scope.mustChoose == 'yes')) {
                                var list = $scope.formatList();
                                if (list.indexOf(value) == -1) {
                                    valid = false;
                                }
                            }

                            if (valid && insig) {
                                valid = insig.findItem(value) === null;
                            }
                            return valid;
                        },
                        deletion: true
                    });
                    
                    var $form = $('body');
                    if ($form.find('> #tag-field-container').length == 0) {
                        $("<div id='tag-field-container'></div>").appendTo($form);
                    }
                    var $container = $form.find('> #tag-field-container');

                    hors = horsey(input, {
                        debounce:250,
                        blankSearch: true,
                        highlighter: false,
                        anchor: false,
                        limit:7,
                        renderCategory: function() {},
                        predictNextSearch: function(info) {
                            insig.refresh();
                            return '';
                        },
                        source: function (data, done) {
                            function resolveItems(items) {
                                done(null, [
                                    { 
                                        id: '', 
                                        list: items.filter(function(item) {
                                            return item.indexOf(data.input) !== -1;
                                        })  
                                    }
                                ]);
                            }
                            $scope.loading = true;
                            $timeout(function() { 
                                if ($scope.dropdown == 'rel') {
                                    if ($scope.formatList().length == 0) {
                                        $http.post(Yii.app.createUrl('formfield/TagField.relnext'), {
                                            's': input.value,
                                            'f': $scope.name,
                                            'm': $scope.modelClass,
                                            'i': $scope.formatList().length
                                        }).success(function (data) {
                                            $scope.loading = false;
                                            if (data && data.count > 0) {
                                                $scope.list = data.list;
                                                $scope.refreshDropdownList();
                                                resolveItems($scope.formatList());
                                            }
                                        });
                                    } else {
                                        $scope.loading = false;
                                    }
                                } else if ($scope.dropdown == 'normal') {
                                    resolveItems($scope.formatList());
                                } else {
                                    resolveItems([]);
                                }
                            });
                        }
                    });
                    
                    input.addEventListener('horsey-hide', function(e) {
                        var w = ((e.target.value.length + 1) * 8);
                        e.target.style.width = (Math.max(w,30)) + 'px';
                    });
                    
                    input.addEventListener('keydown', function(e) {
                        if (e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 188 || e.keyCode == 186) {
                            e.preventDefault();
                            e.stopPropagation();
                            insig.refresh();
                        } 
                    });

                    $(input).on('focus', function () {
                        if ($scope.dropdown != 'none') {
                            if ($scope.unique == 'yes') {
                                $scope.refreshDropdownList();
                            }
                            if ($scope.mustChoose == 'yes') {
                                $timeout(function () {
                                    hors.show();
                                    hors.refreshPosition();
                                });
                            }
                        }
                    });

                    input.addEventListener('insignia-evaluated', function () {
                        $timeout(function () {
                            $scope.value = insig.value();
                            $scope.splitValues();
                            ctrl.$setViewValue($scope.value);
                            hors.refreshPosition();

                        });
                    });

                    input.addEventListener('horsey-selected', function () {
                        insig.convert();

                        if ($scope.unique == 'yes') {
                            $scope.refreshDropdownList();
                        }
                    });

                    if (!$scope.tfLoaded) {
                        $timeout(function () {
                            $scope.tfLoaded = true;
                            $scope.value = insig.value().join($scope.delimiter);
                            $scope.valueArray = insig.value();
                            ctrl.$setViewValue($scope.value);
                        });
                    }
                }

                $timeout(function () {
                    // initialize insignia!
                    input = $el.find('#' + $scope.renderID)[0];
                    $scope.init();
                });

            };
        }
    };
});