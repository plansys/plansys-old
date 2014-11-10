app.directive('psChartGroup', function ($timeout) {
    return {
		scope: true,
		controller: function($scope, $element) {
			
			$scope.data = [];
			$scope.isgroup = true;
			
			$scope.groupTitle = $element.find("data[name=groupTitle]").text();
			$scope.groupName = $element.find("data[name=groupName]").text();
			
			$scope.defaultOptions = {
				chart : {
					renderTo : 'groupContainer' + $scope.groupName
				},
				credits : {
					enabled : false
				}
			};
			
			$scope.redraw = function () {
				var chart = new Highcharts.Chart($scope.defaultOptions);
				chart.setTitle({ text: $scope.groupTitle });
				
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