app.directive('psDataGrid', function($timeout, dateFilter) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {

            return function($scope, $el, attrs, ctrl) {
                function evalArray(array) {
                    for (i in array) {
                        if (typeof array[i] == "string") {
                            if (array[i].match(/true/i)) {
                                array[i] = true;
                            } else if (array[i].match(/false/i)) {
                                array[i] = false;
                            }
                        }
                    }
                }

                $scope.fillColumns = function() {
                    $timeout(function() {
                        var columns = [];

                        var buttonID = 1;
                        for (i in $scope.columns) {
                            var c = $scope.columns[i];

                            evalArray(c.options);

                            if (c.columnType == 'buttons') {
                                var col = angular.extend(c.options, {
                                    field: 'button_' + buttonID,
                                    displayName: c.label,
                                    enableCellEdit: false
                                });
                                buttonID++;
                            } else {
                                var col = angular.extend(c.options, {
                                    field: c.name,
                                    displayName: c.label,
                                });
                            }
                            console.log(col);
                            columns.push(col);
                        }

                        $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];
                        $scope.data = $scope.datasource.data;

                        evalArray($scope.gridOptions);

                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.columnDefs = columns;
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin(),new anchorLastColumn()];
                        $scope.gridOptions.headerRowHeight = 28;
                        $scope.gridOptions.rowHeight = 28;

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