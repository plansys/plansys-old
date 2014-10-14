app.directive('psChartPie', function ($timeout) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {

				$scope.width = $el.find("data[name=width]").text();
				$scope.height = $el.find("data[name=height]").text();
				$scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()].data[0];
				
				$scope.chartData = toChartFormat($scope.datasource);

				$scope.xFunction = function() {
				  return function(d) {
					return d.key;
				  };
				}
				$scope.yFunction = function() {
				  return function(d) {
					return d.y;
				  };
				}
            }
			
			function toChartFormat(array) {
				var result = [];
				for(key in array) {
					var tmp = {'key': key, 'y' : array[key]}
					result.push(tmp);
				}
				
				return result;
			}			
        }
    };
});