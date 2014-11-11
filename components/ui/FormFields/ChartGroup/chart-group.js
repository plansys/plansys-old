app.directive('psChartGroup', function ($timeout) {
    return {
		scope: true,
		controller: function($scope, $element) {
			
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
			
			$scope.data = [];
			$scope.xAxisGroup;
			$scope.isgroup = true;
			
			$scope.groupTitle = $element.find("data[name=groupTitle]").text();
			$scope.groupName = $element.find("data[name=groupName]").text();
			$scope.yAxisType = $element.find("data[name=yAxisType]").text();
			$scope.groupOptions = JSON.parse($element.find("data[name=groupOptions]").text());
			
			$scope.defaultOptions = {
				chart : {
					renderTo : 'groupContainer' + $scope.groupName
				},
				credits : {
					enabled : false
				}
			};
			
			if($scope.groupOptions == null) {
				$scope.groupOptions = {};
			}
			
			$scope.groupOptions = deepExtend($scope.groupOptions, $scope.defaultOptions);
			
			$scope.setxAxisGroup = function(value) {
				$scope.xAxisGroup = value;
			}
			
			$scope.redraw = function () {
				var chart = new Highcharts.Chart($scope.groupOptions);
				chart.setTitle({ text: $scope.groupTitle });
				
				chart.xAxis[0].setCategories($scope.xAxisGroup);
				
				for(i in $scope.data) {
					eval( i + 'Data(chart, $scope.data[i])');
				}
			}
			
			function lineData(chart, data) {
				for(i in data) {
					for(j in data[i]) {
						data[i][j].type = 'line';
					}
					chart.addSeries(data[i][j]);
				}
			}
			
			function columnData(chart, data) {
				for(i in data) {
					for(j in data[i]) {
						data[i][j].type = 'column';
					}
					chart.addSeries(data[i][j]);
				}
			}
		}
	}
});