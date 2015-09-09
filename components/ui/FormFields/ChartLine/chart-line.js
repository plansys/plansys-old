app.directive('psChartLine', function ($timeout) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {
            var parent = $scope.getParent($scope);

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

            var formatChartData = function (chartData_raw) {
                var chartData = [];
                var xAxis = [];

                for (i in chartData_raw)
                {
                    if (chartData_raw[i].label == $scope.tickSeries) {
                        xAxis = chartData_raw[i].value;
                    }
                    else {
                        var tmp = {};
                        tmp['name'] = chartData_raw[i].label;
                        tmp['data'] = chartData_raw[i].value.map(function (e, i) {
                            var ret = e;
                            try {
                                ret = JSON.parse(e);
                            } catch (e) {
                                ret = e;
                            }
                            return ret;
                        });

                        if (typeof chartData_raw[i].color != 'undefined')
                            tmp['color'] = chartData_raw[i].color

                        chartData.push(tmp);
                    }
                }
                return [chartData, xAxis];
            }

            $scope.$watch('datasource.data', function (n, o) {
                if (n !== o && $scope.datasource != null) {
                    $scope.data = $scope.datasource.data;
                    $scope.fillSeries();
                }
            }, true);

            $scope.fillSeries = function () {
                $timeout(function () {
                    var series = [];
                    var chartData = [];
                    $scope.datasource = parent[$el.find("data[name=datasource]").text()];

                    if (typeof $scope.datasource != "undefined") {
                        $scope.data = $scope.datasource.data;
                    } else {
                        $scope.data = [];
                    }

                    var chartData = [];

                    if ($scope.data.length > 0)
                    {
                        var filtered = {};
                        for (var i in $scope.data) {
                            for (var j in $scope.data[i]) {
                                if (typeof filtered[j] == "undefined") {
                                    filtered[j] = [];
                                }

                                filtered[j].push($scope.data[i][j]);
                            }
                        }

                        var result = [];
                        result[0] = [];
                        for (var i in filtered) {
                            var series = {};
                            series.label = i;
                            series.value = filtered[i];
                            result[0][i] = (series);
                        }


                        var chartData_raw = result[0];

                        var formatChart = formatChartData(chartData_raw);
                        var chartData = formatChart[0];
                        var xAxis = formatChart[1];

                        if ($scope.series != null) {
                            for (i in $scope.series) {
                                $scope.series[i] = angular.extend($scope.series[i], chartData_raw[$scope.series[i].label]);
                            }

                            chartData_raw = $scope.series;

                            formatChart = formatChartData(chartData_raw);
                            chartData = formatChart[0];
                            xAxis = formatChart[1];
                        }
                    }

                    var defaultOptions = {
                        chart: {
                            type: $scope.chartType,
                            renderTo: $scope.chartType + 'Container' + $scope.chartName
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            labels: {
                                rotation: 90
                            }
                        }
                    }

                    if ($scope.options == null) {
                        $scope.options = {};
                    }

                    $scope.options = deepExtend(defaultOptions, $scope.options);

                    var chart = new Highcharts.Chart($scope.options);

                    chart.setTitle({text: $scope.chartTitle});
                    chart.xAxis[0].setCategories(xAxis);

                    for (i in chartData) {
                        chart.addSeries(chartData[i]);
                    }

                    if (typeof parent.isgroup != 'undefined' && parent.isgroup) {
                        if (typeof parent.data[$scope.chartType] == 'undefined')
                            parent.data[$scope.chartType] = [];

                        parent.data[$scope.chartType].push(chart);
                        parent.setxAxisGroup(xAxis);
                        
                        $el.hide();
                        parent.redraw();
                    }

                }, 0);
            }

            $scope.chartTitle = $el.find("data[name=chartTitle]").text();
            $scope.chartType = $el.find("data[name=chartType]").text().toLowerCase();
            $scope.chartName = $el.find("data[name=chartName]").text();
            $scope.series = JSON.parse($el.find("data[name=series]").text());
            $scope.tickSeries = $el.find("data[name=tickSeries]").text();
            $scope.options = JSON.parse($el.find("data[name=options]").text());

            $scope.fillSeries();

        }
    }
});