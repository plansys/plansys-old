app.directive('psChartGroup', function ($timeout) {
    return {
        scope: true,
        controller: function ($scope, $element) {

            /*********************** DEEP EXTEND ********************************/ 
            var deepExtend = function (destination, source) {
                for (var property in source) {
                    if (source[property] && source[property].constructor &&
                            source[property].constructor === Object) {

                        destination[property] = destination[property] || {};
                        arguments.callee(destination[property], source[property]);
                    } else {
                        if (typeof source[property] != "undefined") {
                            if (typeof source[property] == "string") {
                                destination[property] = source[property];

                                if (destination[property].substr(0, 3) == "js:") {
                                    destination[property] = $scope.$eval(destination[property].substr(3));
                                }
                            } else {
                                destination[property] = source[property];
                            }
                        } else {
                            destination[property] = undefined;
                        }
                    }
                }

                return destination;
            }

            $scope.data = [];
            $scope.yAxisGroup = [];
            $scope.xAxisGroup = null;
            $scope.isgroup = true;

            $scope.groupTitle = $element.find("data[name=groupTitle]").text();
            $scope.groupName = $element.find("data[name=groupName]").text();
            $scope.yAxisType = $element.find("data[name=yAxisType]").text();
            $scope.groupOptions = JSON.parse($element.find("data[name=groupOptions]").text());
            $scope.isPieGroup = JSON.parse($element.find("data[name=isPieGroup]").text().toLowerCase());

            $scope.defaultOptions = {
                chart: {
                    renderTo: 'groupContainer' + $scope.groupName
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    labels: {
                        rotation: 90
                    }
                },
            };

            if ($scope.yAxisType.toLowerCase() == "double") {
                var shared = {
                    tooltip: {
                        shared: true
                    }
                }

                $scope.defaultOptions = deepExtend($scope.defaultOptions, shared);

            }

            if ($scope.groupOptions == null) {
                $scope.groupOptions = {};
            }

            $scope.groupOptions = deepExtend($scope.groupOptions, $scope.defaultOptions);

            $scope.setxAxisGroup = function (value) {
                $scope.xAxisGroup = value;
            }

            $scope.redraw = function () {
                var series = [];
                var yAxis = [];

                var chart = new Highcharts.Chart($scope.groupOptions);
                chart.setTitle({text: $scope.groupTitle});

                chart.xAxis[0].setCategories($scope.xAxisGroup);


                var count = 0;
                for (i in $scope.data) {

                    var idx = 0;
                    while (typeof $scope.data[i][idx].yAxis == "undefined" && idx < $scope.data[i].length) {
                        idx++;
                    }

                    var title = '';
                    if ($scope.data[i][idx].yAxis[0].axisTitle && $scope.data[i][idx].yAxis[0].axisTitle.textStr) {
                        title = $scope.data[i][idx].yAxis[0].axisTitle.textStr;
                    }

                    var tmpyAxis = {
                        'title': {
                            'text': title
                        }
                    };

                    if (count % 2 == 1) {
                        tmpyAxis.opposite = true;
                    }

                    yAxis.push(tmpyAxis);

                    for (j in $scope.data[i][idx].series) {
                        var tmpData = {
                            'color': $scope.data[i][idx].series[j].color,
                            'name': $scope.data[i][idx].series[j].name,
                            'type': $scope.data[i][idx].series[j].type,
                            'data': $scope.data[i][idx].series[j].yData,
                        }

                        if ($scope.yAxisType.toLowerCase() == "double")
                            tmpData.yAxis = count;
                        
                        series.unshift(tmpData);
                    }

                    count++;
                }
                chart.yAxis[0].update(yAxis[0]);

                for (i in series) {
                    if (typeof chart.yAxis[series[i].yAxis] == "undefined" &&
                            $scope.yAxisType.toLowerCase() == "double") {
                        chart.addAxis(yAxis[series[i].yAxis]);
                    }
                    chart.addSeries(series[i]);
                }

                chart.redraw();
            }

            function lineData(chart, data) {
                for (i in data) {
                    for (j in data[i]) {
                        data[i][j].type = 'line';
                        chart.addSeries(data[i][j]);
                    }
                }
            }

            function columnData(chart, data) {
                for (i in data) {
                    for (j in data[i]) {
                        data[i][j].type = 'column';
                        chart.addSeries(data[i][j]);
                    }
                }
            }
        }
    }
});