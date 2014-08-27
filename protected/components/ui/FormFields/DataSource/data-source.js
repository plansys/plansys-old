app.directive('psDataSource', function($timeout) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {
            return function($scope, $el, attrs, ctrl) {
                $scope.data = JSON.parse($el.find("data[name=data]").text());
                $scope.name = $el.find("data[name=name]").text().trim();
                
                $scope.reload = function() {
                    
                }
                
                
                $scope.$parent[$scope.name] = $scope;
            }

        }
    };
});