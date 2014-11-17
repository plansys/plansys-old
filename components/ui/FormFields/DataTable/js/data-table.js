app.directive('psDataTable', function ($timeout, $http, $compile, dateFilter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];
                
                var colHeaders = [];
                var columns = [];
                for (i in $scope.columns) {
                    var c = $scope.columns[i];
                    colHeaders.push(c.label);
                    columns.push({
                        data: c.name
                    });
                }

                var options = {
                    data: $scope.datasource.data,
                    minSpareRows: 1,
                    columnSorting: true,
                    contextMenu: true,
                    colHeaders: colHeaders,
                    columns: columns,
                    contextMenu: ['row_above', 'row_below','---------', 'remove_row','---------', 'undo', 'redo']
                };

                $("#" + $scope.renderID).handsontable(options);
            }
        }
    }
});