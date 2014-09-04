app.directive('psDataGrid', function($timeout, dateFilter) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {

            return function($scope, $el, attrs, ctrl) {
                $scope.fillColumns = function() {
                    $timeout(function() {
                        var columns = [];

                        for (i in $scope.columns) {
                            var c = $scope.columns[i];
                            columns.push({
                                field: c.name,
                                displayName: c.label
                            });
                        }

                        $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];
                        $scope.data = $scope.datasource.data;

                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.columnDefs = columns;
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin()];
                        $scope.loaded = true;
                    }, 0);
                }

                $scope.name = $el.find("data[name=name]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.loaded = false;
                $scope.fillColumns();
            }
        }
    };
});