app.directive('dateTimePicker', function ($timeout, dateFilter) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                var fieldType = element.find("data[name=field_type]").text();
                // if (fieldType == 'date') {
                //     attrs.$set('ngModel', '$parent.$parent.' + attrs.ngModel, false);
                // } else {
                    attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
                // }
            }

            return function ($scope, $el, attrs, ctrl) {
                
                // when ng-model is changed from inside directive
                $scope.update = function () {
                    switch ($scope.fieldType) {
                        case 'datetime':
                            var time = dateFilter($scope.time, 'HH:mm:00');
                            $scope.value = $scope.date + " " + time;
                            break;
                        case 'datepicker':
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

                $scope.changeDropdown = function () {
                    if ($scope.dd.year == 'Other') {
                        $scope.dd.year = prompt('What year?');
                        if (isNaN($scope.dd.year) || $scope.dd.year === null) {
                            $scope.dd.year = $scope.yearList[0];
                        } else if ($scope.yearList.indexOf($scope.dd.year) < 0) {
                            $scope.yearList.unshift($scope.dd.year);
                        }
                    }
                    var maxDay = (new Date($scope.dd.year, $scope.dd.month + 1, 0)).getDate();
                    if ($scope.dd.day * 1 > maxDay) {
                        $scope.dd.day = maxDay;
                    }

                    $scope.dd.day = $scope.dd.day < 10 ? "0" + ($scope.dd.day * 1) : $scope.dd.day + "";
                    var month = $scope.dd.month + 1 < 10 ? "0" + ($scope.dd.month + 1) : $scope.dd.month + 1;
                    $scope.value = $scope.dd.year + "-" + month + "-" + $scope.dd.day;
                    
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
                                case 'datepicker':
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
                        case "datepicker":
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
                        case "date":
                            var dd = split[0].split("-");
                            if (dd.length > 1) {
                                $scope.dd.month = dd[1] - 1;
                                $scope.dd.year = dd[0];
                                $scope.dd.day = (dd.length > 2 ? dd[2] : 1);

                                var maxDay = (new Date($scope.dd.year, $scope.dd.month + 1, 0)).getDate();
                                if ($scope.dd.day *1 > maxDay) {
                                    $scope.dd.day = maxDay;
                                    $scope.value = $scope.dd.year + "-" + ($scope.dd.month +1) + '-' + $scope.dd.day;
                                    ctrl.$setViewValue($scope.value);
                                }
                            } else {
                                if ($scope.defaultToday == 'Yes') {
                                    $scope.dd.month = window.date("m") -1;
                                    $scope.dd.year = window.date("Y"); 
                                    $scope.dd.day = window.date("d");
                                    $scope.value = window.date("Y-m-d");
                                } else {
                                    $scope.dd.month = '';
                                    $scope.dd.year = ''; 
                                    $scope.dd.day = '';
                                    $scope.value = null;                                    
                                }
                                ctrl.$setViewValue($scope.value);
                            }
                            if ($scope.dd.day < 10 && $scope.dd.day > 0) {
                                $scope.dd.day = "0" + ($scope.dd.day * 1);
                            }

                            var y = window.date("Y") * 1;
                            var startYear = y-1;
                            var endYear = y+1;
                            if (!!$scope.dateOptions['start-year']) {
                                startYear = $scope.parseYear($scope.dateOptions['start-year']);
                            }  
                            if (!!$scope.dateOptions['end-year']) {
                                endYear = $scope.parseYear($scope.dateOptions['end-year']);
                            }
                            $scope.yearList = [];
                            if ($scope.defaultToday != 'Yes') {
                                $scope.yearList.push('');
                            }
                            
                            if (startYear >= endYear) {
                                for (var i = endYear; i > startYear; i--) {
                                    $scope.yearList.push(i + "");
                                }
                            } else {
                                for (var i = startYear; i <= endYear; i++) {
                                    $scope.yearList.push(i + "");
                                }
                            }

                            if ($scope.dateOptions['hide-other-year'] !== 'true') {
                                $scope.yearList.push('Other');
                            }
                        break;
                    }
                }


                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (!ctrl.$viewValue && $scope.defaultToday == 'Yes') {
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
                $scope.name = $el.find("data[name=name]").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.defaultToday = $el.find("data[name=default_today]").html().trim();
                $scope.date = null;
                $scope.time = "";
                $scope.splitDateTime();
                $scope.dd = {
                    day: "",
                    month: "",
                    year: ""
                };
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.disabledCondition = $el.find("data[name=is_disabled]").text().trim();
                $scope.fieldType = $el.find("data[name=field_type]").text();
                $scope.dateOptions = JSON.parse($el.find("data[name=date_options]").text());
                
                $scope.dayList = [
                    {i:"01",n:"01"},
                    {i:"02",n:"02"},
                    {i:"03",n:"03"},
                    {i:"04",n:"04"},
                    {i:"05",n:"05"},
                    {i:"06",n:"06"},
                    {i:"07",n:"07"},
                    {i:"08",n:"08"},
                    {i:"09",n:"09"},
                    {i:"10",n:"10"},
                    {i:"11",n:"11"},
                    {i:"12",n:"12"},
                    {i:"13",n:"13"},
                    {i:"14",n:"14"},
                    {i:"15",n:"15"},
                    {i:"16",n:"16"},
                    {i:"17",n:"17"},
                    {i:"18",n:"18"},
                    {i:"19",n:"19"},
                    {i:"20",n:"20"},
                    {i:"21",n:"21"},
                    {i:"22",n:"22"},
                    {i:"23",n:"23"},
                    {i:"24",n:"24"},
                    {i:"25",n:"25"},
                    {i:"26",n:"26"},
                    {i:"27",n:"27"},
                    {i:"28",n:"28"},
                    {i:"29",n:"29"},
                    {i:"30",n:"30"},
                    {i:"31",n:"31"}
                ];
                
                if ($scope.dateOptions['short-month'] == 'true') {
                    $scope.monthList = [
                        {i:0,n:"Jan"}, 
                        {i:1,n:"Feb"},
                        {i:2,n:"Mar"},
                        {i:3,n:"Apr"},
                        {i:4,n:"Mei"},
                        {i:5,n:"Jun"},
                        {i:6,n:"Jul"},
                        {i:7,n:"Ags"},
                        {i:8,n:"Sep"},
                        {i:9,n:"Okt"},
                        {i:10,n:"Nov"},
                        {i:11,n:"Des"}
                    ];
                } else {
                    $scope.monthList = [
                        {i:0,n:"Januari"}, 
                        {i:1,n:"Februari"},
                        {i:2,n:"Maret"},
                        {i:3,n:"April"},
                        {i:4,n:"Mei"},
                        {i:5,n:"Juni"},
                        {i:6,n:"Juli"},
                        {i:7,n:"Agustus"},
                        {i:8,n:"September"},
                        {i:9,n:"Oktober"},
                        {i:10,n:"November"},
                        {i:11,n:"Desember"}
                    ];
                }
                
                if ($scope.defaultToday != 'Yes') {
                    $scope.monthList.unshift({i:'',n:''});
                    $scope.dayList.unshift({i:'',n:''});
                }
                
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
                        var ngModelValue = $scope.$parent.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                        $scope.splitDateTime();
                    }, 0);
                }
            }
        }
    };
});