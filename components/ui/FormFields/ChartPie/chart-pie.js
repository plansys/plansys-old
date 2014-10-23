app.directive('psChartPie', function ($timeout, $http, $compile, dateFilter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
				
				$scope.chartTitle = $el.find("data[name=chartTitle]").text();
				$scope.series = $el.find("data[name=series]").text();
				
				var chartData = [];
				
				$scope.series = jQuery.parseJSON($scope.series);
				
				for(i in $scope.series)
				{
					var tmp = {};
					tmp['name'] = $scope.series[i].label;
					tmp['y'] = parseInt($scope.series[i].value);
					tmp['color'] = $scope.series[i].color;
					
					chartData.push(tmp);
				}
				
				console.log(chartData);
				
				$('#container').highcharts({
					chart: {
						plotBackgroundColor: null,
						plotBorderWidth: 1,//null,
						plotShadow: false
					},
					title: {
						text: $scope.chartTitle
					},
					tooltip: {
						pointFormat: 'Percentage: <b>{point.percentage:.1f}%</b>'
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								format: '<b>{point.name}</b>: {point.percentage:.1f} %',
								style: {
									color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
								}
							}
						}
					},
					series: [{
						type: 'pie',
						name: 'data',
						data: chartData
					}]
				});
			}
		}
	}
});