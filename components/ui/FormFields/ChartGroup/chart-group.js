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
			$scope.yAxisGroup = [];
			$scope.xAxisGroup = null;
			$scope.isgroup = true;
			
			$scope.groupTitle = $element.find("data[name=groupTitle]").text();
			$scope.groupName = $element.find("data[name=groupName]").text();
			$scope.yAxisType = $element.find("data[name=yAxisType]").text();
			$scope.groupOptions = JSON.parse($element.find("data[name=groupOptions]").text());
			$scope.isPieGroup = JSON.parse($element.find("data[name=isPieGroup	]").text().toLowerCase());
			
			$scope.defaultOptions = {
				chart : {
					renderTo : 'groupContainer' + $scope.groupName
				},
				credits : {
					enabled : false
				},
				xAxis : {
					labels : {
						rotation : 90
					}
				},
			};
			
			if($scope.yAxisType.toLowerCase() == "double") {
				var shared = {
					tooltip: {
						shared: true
					}
				}
				
				$scope.defaultOptions = deepExtend($scope.defaultOptions, shared);
				
			}
			
			if($scope.groupOptions == null) {
				$scope.groupOptions = {};
			}
			
			$scope.groupOptions = deepExtend($scope.groupOptions, $scope.defaultOptions);
			
			$scope.setxAxisGroup = function(value) {
				$scope.xAxisGroup = value;
			}
			
			$scope.redraw = function () {			
				var series = [];
				var yAxis = [];
				
				var chart = new Highcharts.Chart($scope.groupOptions);
				chart.setTitle({ text: $scope.groupTitle });
				
				chart.xAxis[0].setCategories($scope.xAxisGroup);
				
				var count = 0;
				for(i in $scope.data) {
					var tmpyAxis = {
						'title' : {
							'text' : $scope.data[i][0].yAxis[0].axisTitle.textStr
						}
					};
					
					if(count%2 == 1) {
						tmpyAxis.opposite = true;
					}
					
					yAxis.push(tmpyAxis);
					
					for( j in $scope.data[i][0].series) {
						var tmpData = {
							'color' : $scope.data[i][0].series[j].color,
							'name' : $scope.data[i][0].series[j].name,
							'type' : $scope.data[i][0].series[j].type,
							'data' : $scope.data[i][0].series[j].yData,
						}
						
						if($scope.yAxisType.toLowerCase() == "double")
							tmpData.yAxis = count;
						
						series.push(tmpData);
					}
					
					count++;
				}
				
				chart.yAxis[0].update(yAxis[0]);
				
				for(i in series) {
					if(typeof chart.yAxis[series[i].yAxis] == "undefined" && $scope.yAxisType.toLowerCase() == "double")
						chart.addAxis(yAxis[series[i].yAxis]);
					chart.addSeries(series[i]);
				}
				
				chart.redraw();
			}
			
			function lineData(chart, data) {
				for(i in data) {
					for(j in data[i]) {
						data[i][j].type = 'line';
						chart.addSeries(data[i][j]);
					}
				}
			}
			
			function columnData(chart, data) {
				for(i in data) {
					for(j in data[i]) {
						data[i][j].type = 'column';
						chart.addSeries(data[i][j]);
					}
				}
			}
		}
	}
});