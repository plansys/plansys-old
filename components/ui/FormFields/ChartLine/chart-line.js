app.directive('psChartLine', function ($timeout) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
				
				/*********************** DEEP EXTEND ********************************/
				var deepExtend = function (destination, source) {
				  for (var property in source) {
					if (source[property] && source[property].constructor &&
					 source[property].constructor === Object) {
					  destination[property] = destination[property] || {};
					  arguments.callee(destination[property], source[property]);
					} else {
					  destination[property] = source[property];
					}
				  }
				  return destination;
				}
				
				$scope.chartTitle = $el.find("data[name=chartTitle]").text();
				$scope.series = JSON.parse($el.find("data[name=series]").text());
				$scope.tickSeries = $el.find("data[name=tickSeries]").text();
				$scope.options = JSON.parse($el.find("data[name=options]").text());	
				
				var xAxis = [];
				var chartData = [];				
				for(i in $scope.series)
				{
					if($scope.series[i].label == $scope.tickSeries) {
						xAxis = $scope.series[i].value;
					}
					else {	
						var tmp = {};
						tmp['name'] = $scope.series[i].label;
						tmp['data'] = $scope.series[i].value.map(Number);
						tmp['color'] = $scope.series[i].color;
						chartData.push(tmp);
					}
				}
				
				var defaultOptions = {
					chart : {
						type: 'line',
						renderTo : 'lineContainer' + $scope.chartTitle
					},
					credits : {
						enabled : false
					}
				}
				
				if($scope.options == null) {
					$scope.options = {};
				}
				
				$scope.options = deepExtend($scope.options, defaultOptions);
				
				var chart = new Highcharts.Chart($scope.options);
				
				chart.setTitle({ text: $scope.chartTitle });
				chart.xAxis[0].setCategories(xAxis);
				
				for(i in chartData) {
					chart.addSeries(chartData[i]);
				}
			}
		}
	}
});