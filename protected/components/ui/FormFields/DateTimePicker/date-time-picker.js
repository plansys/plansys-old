app.directive('dateTimePicker', function($timeout, dateFilter) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.update = function() {
                    $scope.value = $scope.date + " " + $scope.time;
                    if (typeof ctrl != 'undefined') {
                        $el.find('ul[datepicker-popup-wrap]').hide();
                        $timeout(function() {
                            ctrl.$setViewValue($scope.value);
                        }, 0);
                    }
                };

                $scope.changeDate = function(e) {
                    $scope.date = dateFilter(e.date, 'yyyy-MM-dd');
                    $scope.update();
                }

                $scope.changeTime = function(e) {
                    $scope.time = dateFilter(e.time, 'HH:mm:00');
                    $scope.update();
                }

                $scope.openDatePicker = function($event) {
                    $event.preventDefault();
                    $event.stopPropagation();

                    if ($el.find('ul[datepicker-popup-wrap]').is(':visible')) {
                        $el.find('ul[datepicker-popup-wrap]').hide();
                    } else {
                        $el.find('ul[datepicker-popup-wrap]').show();
                        $el.find('ul[datepicker-popup-wrap] table').attr('mouse-inside', '1').focus();
                    }
                };

                $el.on({
                    blur: function() {
                        if ($(this).attr('mouse-inside') == '0') {
                            $el.find('ul[datepicker-popup-wrap]').hide();
                        }
                    },
                    mouseover: function() {
                        $(this).attr('mouse-inside', '1');
                    },
                    mouseout: function() {
                        $(this).attr('mouse-inside', '0');
                    },
                }, 'ul[datepicker-popup-wrap] table');

                $scope.splitTime = function(delimiter, time) {
                    var ret = {};
                    if (time.split(delimiter).length > 1) {
                        ret.hour = time.split(delimiter)[0];
                        ret.min = time.split(delimiter)[1];
                    }
                    return ret;
                }

                $scope.parseTime = function(time) {
                    time = time.trim();
                    var newtime = {};
                    if (time.split(":").length > 1) {
                        newtime = $scope.splitTime(":", time);
                    } else if (time.split(".").length > 1) {
                        newtime = $scope.splitTime(".", time);
                    } else if (time.split(" ").length > 1) {
                        newtime = $scope.splitTime(" ", time);
                    }

                    var d = new Date();
                    d.setHours(newtime.hour);
                    d.setMinutes(newtime.min);
                    return d;
                }

                $scope.splitDateTime = function() {
                    if ($scope.value == null || ($scope.value != null && $scope.value.trim() == '')) {
                        $scope.value = dateFilter(new Date(), 'yyyy-MM-dd HH:mm:00');
                    }

                    var split = $scope.value.trim().split(' ');

                    // switch it based on fieldType
                    switch ($scope.fieldType) {
                        case "date":
                            if (split.length == 1) {
                                $scope.date = split[0];
                            } else if (split.length == 2) {
                                $scope.date = split[0];
                                $scope.time = $scope.parseTime(split[1]);
                            }
                            break;
                        case "time":
                            if (split.length == 1) {
                                $scope.time = $scope.parseTime(split[0]);
                            } else if (split.length == 2) {
                                $scope.date = split[0];
                                $scope.time = $scope.parseTime(split[1]);
                            }
                            break;
                        case "datetime":
                            $scope.date = split[0];

                            if (split.length > 1) {
                                $scope.time = $scope.parseTime(split[1]);
                            }
                            break;
                    }

                }

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function() {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.splitDateTime();
                            $scope.update();
                        }
                    };
                }

                // set default value
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.date = new Date();
                $scope.time = "";
                $scope.splitDateTime();

                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";
                $scope.fieldType = $el.find("data[name=field_type]").text();
                $scope.dateOptions = JSON.parse($el.find("data[name=date_options]").text());

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function() {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                            $scope.splitDateTime();
                        }
                    }, 0);
                }
            }
        }
    };
});