app.directive('psChartArea', function ($timeout) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {
				
			/*********************** DEEP EXTEND ********************************/
			var deepExtend = function (destination, source) {
			  for (var property in source) {
				if (source[property] && source[property].constructor &&
				 source[property].constructor === Object) {
				  destination[property] = destination[property] || {};
				  arguments.callee(destination[property], source[property]);
				} else {
				  destination[property] = isNaN(source[property]) ? source[property] : parseInt(source[property]);
				}
			  }
			  return destination;
			}
			
			$scope.chartTitle = $el.find("data[name=chartTitle]").text();
			$scope.chartName = $el.find("data[name=chartName]").text();
			$scope.chartType = $el.find("data[name=chartType]").text().toLowerCase();
			$scope.tickSeries = $el.find("data[name=tickSeries]").text();
			$scope.series = JSON.parse($el.find("data[name=series]").text());
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
					tmp['data'] = $scope.series[i].value.map(function(e, i){ return JSON.parse(e)});
					tmp['color'] = $scope.series[i].color;
					chartData.push(tmp);
				}
			}
			
			var defaultOptions = {
				chart : {
					type: $scope.chartType,
					renderTo : $scope.chartType + 'Container' + $scope.chartName
				},
				credits : {
					enabled : false
				},
				xAxis : {
					labels : {
						rotation : 90
					}
				},
			}
			
			if($scope.options == null) {
				$scope.options = {};
			}
			
			$scope.options = deepExtend(defaultOptions, $scope.options);
			
			if(typeof $scope.isgroup != 'undefined' && $scope.isgroup) {
				if(typeof $scope.data[$scope.chartType] == 'undefined')
					$scope.data[$scope.chartType] = [];
				
				$scope.data[$scope.chartType].push(chart);
				$scope.setxAxisGroup(xAxis);
				
				$el.hide();
				$scope.redraw();
			} else {
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