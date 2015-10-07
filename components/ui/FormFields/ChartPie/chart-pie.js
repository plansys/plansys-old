app.directive('psChartPie', function ($timeout) {
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
                for (i in chartData_raw)
                {
                    var tmp = {};
                    tmp['name'] = chartData_raw[i].label;
                    tmp['y'] = parseInt(chartData_raw[i].value);
                    if (typeof chartData_raw[i].color != 'undefined')
                        tmp['color'] = chartData_raw[i].color

                    chartData.push(tmp);
                }

                return chartData;
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
                        var filtered = [];
                        for (var i in $scope.data) {
                            var rowcontent = {};
                            for (var j in $scope.data[i]) {
                                rowcontent[j] = $scope.data[i][j];
                            }
                            filtered.push(rowcontent);
                        }

                        var result = [];
                        for (var i in filtered) {
                            if (typeof result[i] == "undefined") {
                                result[i] = [];
                            }

                            for (var j in filtered[i]) {
                                var series = {};
                                series.value = filtered[i][j];
                                series.label = j;

                                result[i][j] = (series);
                            }
                        }

                        var chartData_raw = result[0];
                        chartData = formatChartData(chartData_raw);

                        if ($scope.series != null) {
                            for (i in $scope.series) {
                                $scope.series[i] = angular.extend($scope.series[i], chartData_raw[$scope.series[i].label]);
                            }

                            chartData_raw = $scope.series;
                            chartData = formatChartData(chartData_raw);
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
                        plotOptions: {
                            pie: {
                                dataLabels: {
                                    format: '<i>{point.percentage:.1f}%</i>',
                                    color: 'white',
                                    distance: -20
                                }
                            }
                        }
                    }

                    if ($scope.options == null) {
                        $scope.options = {};
                    }

                    $scope.options = deepExtend(defaultOptions, $scope.options);


                    if (typeof $scope.isgroup != 'undefined' && $scope.isgroup) {
                        if (typeof $scope.data[$scope.chartType] == 'undefined')
                            $scope.data[$scope.chartType] = [];

                        $scope.data[$scope.chartType].push({
                            name: 'value',
                            data: chartData
                        });

                        $el.hide();
                        $scope.redraw();
                    } else {
                        var chart = new Highcharts.Chart($scope.options);

                        chart.setTitle({text: $scope.chartTitle});
                        chart.addSeries({
                            name: 'value',
                            data: chartData
                        });
                    }

                }, 0);
            }

            $scope.chartTitle = $el.find("data[name=chartTitle]").text();
            $scope.chartType = $el.find("data[name=chartType]").text().toLowerCase();
            $scope.chartName = $el.find("data[name=chartName]").text();
            $scope.series = JSON.parse($el.find("data[name=series]").text());
            $scope.options = JSON.parse($el.find("data[name=options]").text());
            $scope.fillSeries();

        }
    }
});