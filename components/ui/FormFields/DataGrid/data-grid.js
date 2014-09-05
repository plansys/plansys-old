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

                $scope.generateButtons = function(buttons) {
                    var html = '<div class="ngCellButton colt{{$index}}">';
                    for (i in buttons) {
                        var b = buttons[i];
                        if (b.url.match(/http*/ig)) {
                            var url = "{{'" + b.url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'") + "'}}";
                        } else {
                            var url = "{{Yii.app.createUrl('" + b.url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'") + "')}}";
                        }
                        html += '<a href="' + url + '" class="btn btn-xs btn-default"><i class="' + b.icon + '"></i></a>';
                    }
                    html += '</div>';
                    return html;
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
                                    enableCellEdit: false,
                                    cellTemplate: $scope.generateButtons(c.buttons)
                                });
                                buttonID++;
                            } else {
                                var col = angular.extend(c.options, {
                                    field: c.name,
                                    displayName: c.label,
                                });
                            }
                            columns.push(col);
                        }

                        $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];
                        $scope.data = $scope.datasource.data;

                        evalArray($scope.gridOptions);

                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.columnDefs = columns;
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin(), new anchorLastColumn()];
                        $scope.gridOptions.headerRowHeight = 28;
                        $scope.gridOptions.rowHeight = 28;

                        if (typeof $scope.onGridLoaded == 'function') {
                            $scope.onGridLoaded($scope.gridOptions);
                        }

                        $scope.loaded = true;
                    }, 0);
                }

                $scope.name = $el.find("data[name=name]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.loaded = false;
                $scope.onGridLoaded = '';
                $scope.fillColumns();

                $scope.$parent[$scope.name] = $scope;
            }
        }
    };
});