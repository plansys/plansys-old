app.directive('dateTimePicker', function ($timeout, dateFilter) {
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
                    switch ($scope.fieldType) {
                        case 'datetime':
                            var time = dateFilter($scope.time, 'HH:mm:00');
                            $scope.value = $scope.date + " " + time;
                            break;
                        case 'date':
                            $scope.value = $scope.date;
                            break;
                        case 'monthyear':
                            $scope.value = $scope.date;
                            break;
                        case 'time':
                            var time = dateFilter($scope.time, 'HH:mm:00');
                            $scope.value = time;
                            break;
                    }

                    if (!!ctrl) {
                        $el.find('ul[datepicker-popup-wrap]').hide();
                        $timeout(function () {
                            ctrl.$setViewValue($scope.value);
                        }, 0);
                    }
                };

                $scope.changeDate = function (e) {
                    $scope.date = dateFilter(e.date, 'yyyy-MM-dd');
                    $scope.update();
                }

                $scope.changeTime = function (e) {
                    $scope.time = dateFilter(e.time, 'HH:mm:00');
                    $scope.update();
                }

                $scope.openDatePicker = function ($event) {
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
                    blur: function () {
                        if ($(this).attr('mouse-inside') == '0') {
                            $el.find('ul[datepicker-popup-wrap]').hide();
                        }
                    },
                    mouseover: function () {
                        $(this).attr('mouse-inside', '1');
                    },
                    mouseout: function () {
                        $(this).attr('mouse-inside', '0');
                    },
                }, 'ul[datepicker-popup-wrap] table');

                $scope.splitTime = function (delimiter, time) {
                    var ret = {};
                    if (time.split(delimiter).length > 1) {
                        ret.hour = time.split(delimiter)[0];
                        ret.min = time.split(delimiter)[1];
                    }
                    return ret;
                }

                $scope.parseTime = function (time) {
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
                
                $scope.parseYear = function(year) {
                    if (!!year) {
                        if (year.indexOf("+") >= 0 || year.indexOf("-") >= 0) {
                            return window.date("Y") * 1 + parseInt(year);
                        } 
                        
                        return year;
                    } 
                    return window.date("Y");
                }

                $scope.splitDateTime = function () {
                    var isWrongValue = false;
                    if (typeof $scope.value === "string") {
                        if ($scope.value.trim() == '') {
                            isWrongValue = true;
                        }
                    } else {
                        isWrongValue = true;
                    }
                    
                    if (isWrongValue || $scope.value == '0000-00-00' 
                        || $scope.value == '0000-00-00 00:00:00') {
                        if ($scope.defaultToday == 'Yes') {
                            switch ($scope.fieldType) {
                                case 'datetime':
                                    $scope.value = dateFilter(new Date(), 'yyyy-MM-dd HH:mm:00');
                                    break;
                                case 'date':
                                    $scope.value = dateFilter(new Date(), 'yyyy-MM-dd');
                                    break;
                                case 'time':
                                    $scope.value = dateFilter(new Date(), 'HH:mm:00');
                                    break;
                                case 'monthyear':
                                    $scope.value = dateFilter(new Date(), 'yyyy-MM-01');
                            }
                        } else {
                            $scope.value = '';
                        }
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
                        case "monthyear":
                            var dd = split[0].split("-");
                            if (dd.length > 1) {
                                $scope.month = dd[1] - 1;
                                $scope.year = dd[0];
                            } else {
                                $scope.month = window.date("m") -1;
                                $scope.year = window.date("Y"); 
                                $scope.value = window.date("Y-m-d");
                                ctrl.$setViewValue($scope.value);
                            }
                            var y = $scope.year * 1;
                            var startYear = y-1;
                            var endYear = y+1;
                            if (!!$scope.dateOptions['start-year']) {
                                startYear = $scope.parseYear($scope.dateOptions['start-year']);
                            }  
                            if (!!$scope.dateOptions['start-year']) {
                                endYear = $scope.parseYear($scope.dateOptions['end-year']);
                            }
                            if (startYear > endYear) {
                                var tempYear = startYear;
                                startYear = endYear;
                                endYear = tempYear;
                            }
                            
                            $scope.yearList = [];
                            for (var i = startYear; i < endYear; i++) {
                                $scope.yearList.push(i);
                            }
                    }
                }

                $scope.changeMonth = function (m) {
                    $scope.month = m;

                    var month = $scope.month < 10 ? "0" + ($scope.month + 1) : $scope.month + 1;
                    $scope.value = $scope.year + "-" + month + "-01";
                    ctrl.$setViewValue($scope.value);
                }

                $scope.changeYear = function (y) {
                    $scope.year = y;

                    var month = $scope.month < 10 ? "0" + ($scope.month + 1) : $scope.month + 1;
                    $scope.value = $scope.year + "-" + month + "-01";
                    ctrl.$setViewValue($scope.value);
                }

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;
                            
                        if (!ctrl.$viewValue) {
                            ctrl.$setViewValue(window.date("Y-m-d"));
                        }

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.splitDateTime();
                            $scope.update();
                        }
                    };
                }

                // set default value
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.defaultToday = $el.find("data[name=default_today]").html().trim();
                $scope.date = null;
                $scope.time = "";
                $scope.splitDateTime();
                $scope.monthList = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.disabledCondition = $el.find("data[name=is_disabled]").text().trim();
                $scope.fieldType = $el.find("data[name=field_type]").text();
                $scope.dateOptions = JSON.parse($el.find("data[name=date_options]").text());
                
                $scope.$watch($scope.disabledCondition, function() {
                    $scope.isDPDisabled = $scope.$eval($scope.disabledCondition);
                });
                $scope.isDPDisabled = false;
                $scope.isDatePickerDisabled = function() {
                    return $scope.isDPDisabled;
                }
                
                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
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