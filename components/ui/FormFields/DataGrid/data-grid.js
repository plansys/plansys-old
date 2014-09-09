app.directive('psDataGrid', function($timeout, $http, $compile, dateFilter) {
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

                $('body').on({
                    mouseover: function() {
                        var $container = $(this);
                        $('.ngRow > .ngCellButtonCollapsedDetail').each(function() {
                            var $container = $(this).parent().find('.' + $(this).attr('colt')).find('.ngCellButtonCollapsed');
                            $(this).hide().remove().appendTo($container);
                        });

                        var $detail = $(this).find('.ngCellButtonCollapsedDetail').remove();
                        var offset = {
                            right: $(this).parents('.ngCanvas').width() -
                                    ($(this).parents('.ngCell').css('left').replace('px', '') * 1 +
                                            $(this).parents('.ngCell').width())
                        };
                        $detail.attr('colt', $(this).parents('.ngCell').attr('class').split(' ').pop())
                                .css(offset)
                                .show();

                        $detail.appendTo($(this).parents('.ngRow'));
                        $compile($detail)(angular.element($container).scope());

                        $detail.on({
                            mouseout: function() {
                                $(this).hide().remove().appendTo($container);
                            }
                        });

                    },
                }, '.ngCellButtonCollapsed');

                $scope.pagingKeypress = function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }

                $scope.removeRow = function(row) {
                    if (typeof row == "undefined" || typeof row.rowIndex != 'number') {
                        return;
                    }

                    var index = row.rowIndex;
                    $scope.gridOptions.selectItem(index, false);
                    $scope.data.splice(index, 1);
                };

                $scope.buttonClick = function(row, e) {
                    $btn = $(e.target);

                    if (!$btn.is('a')) {
                        $btn = $btn.parents('a');
                    }

                    if ($btn.attr('confirm')) {
                        if (!confirm($btn.attr('confirm'))) {
                            return false;
                        }
                    }

                    if ($btn.attr('ajax') == 'true') {
                        if ($btn.attr('ajax-success')) {

                            $http.get($btn.attr('href'))
                                    .success(function(data) {
                                        $scope.$eval($btn.attr('ajax-success'), {row: row, data: data});
                                    })
                                    .error(function(data) {
                                        $scope.$eval($btn.attr('ajax-failed'), {row: row, data: data});
                                    });

                        }

                        e.preventDefault();
                        e.stopPropagation();

                        return false;
                    }
                }

                $scope.generateButtons = function(column) {
                    var buttons = column.buttons;
                    var html = '<div class="ngCellButton colt{{$index}}">';
                    var btnSize = 'btn-xs';

                    if (column.buttonCollapsed == 'Yes') {
                        btnSize = 'btn-sm';
                        html += '<div class="ngCellButtonCollapsed">';
                        html += '<div class="ngCellButtonCollapsedDetail">';
                    }

                    for (i in buttons) {
                        var b = buttons[i];
                        var opt = b.options || {};
                        var attr = [];

                        // create url
                        var url = '';
                        if (b.url.match(/http*/ig)) {
                            url = "{{'" + b.url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'") + "'}}";
                        } else if (b.url.trim() == '#') {
                            url = '#';
                        } else {
                            url = "{{Yii.app.createUrl('" + b.url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'") + "')}}";
                        }

                        // generate attribute
                        opt['ng-click'] = 'buttonClick(row, $event)';
                        opt.class = (opt.class || '') + ' btn ' + btnSize + ' btn-default';
                        opt.href = url;
                        for (i in opt) {
                            attr.push(i + '="' + opt[i] + '"');
                        }

                        // create html
                        html += '<a ' + attr.join(' ') + '><i class="' + b.icon + '"></i></a>';

                    }

                    if (column.buttonCollapsed == 'Yes') {
                        html += '</div>';
                        html += '<span>...</span></div>';
                    }

                    html += '</div>';
                    return html;
                }

                $scope.initGrid = function() {
                    $scope.grid = this;
                }

                $scope.fillColumns = function() {
                    $timeout(function() {
                        var columns = [];

                        var buttonID = 1;
                        for (i in $scope.columns) {
                            var c = $scope.columns[i];

                            // prepare columns
                            evalArray(c.options);
                            if (c.columnType == 'buttons') {
                                var col = angular.extend(c.options, {
                                    field: 'button_' + buttonID,
                                    displayName: c.label,
                                    enableCellEdit: false,
                                    sortable: false,
                                    cellTemplate: $scope.generateButtons(c)
                                });

                                if (c.buttonCollapsed == 'Yes') {
                                    col.width = 30;
                                } else {
                                    col.width = (c.buttons.length * 24) + ((c.buttons.length - 1) * 5) + 20;
                                }
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

                        // prepare gridOptions
                        evalArray($scope.gridOptions);
                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.columnDefs = columns;
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin(), new anchorLastColumn()];
                        $scope.gridOptions.headerRowHeight = 28;
                        $scope.gridOptions.rowHeight = 28;

                        // pagingOptions
                        if ($scope.gridOptions['enablePaging']) {
                            $scope.gridOptions.pagingOptions = {
                                pageSizes: [25, 50, 100],
                                pageSize: 25,
                                totalServerItems: $scope.datasource.totalItems,
                                currentPage: 1
                            };
                            $scope.$watch('gridOptions.pagingOptions', function(paging, oldpaging) {
                                if (paging != oldpaging) {
                                    var ds = $scope.datasource;
                                    var maxPage = Math.ceil($scope.datasource.totalItems / $scope.gridOptions.pagingOptions.pageSize);
                                    
                                    if (isNaN($scope.gridOptions.pagingOptions.currentPage) || $scope.gridOptions.pagingOptions.currentPage == '') {
                                        $scope.gridOptions.pagingOptions.currentPage = 1;
                                    }
                                    
                                    if ($scope.gridOptions.pagingOptions.currentPage > maxPage) {
                                        $scope.gridOptions.pagingOptions.currentPage = maxPage;
                                    }

                                    if (typeof ds != "undefined") {
                                        ds.updateParam('currentPage', paging.currentPage, 'paging');
                                        ds.updateParam('pageSize', paging.pageSize, 'paging');
                                        ds.updateParam('totalServerItems', paging.totalServerItems, 'paging');
                                        ds.query();
                                    }
                                }
                            }, true);
                        }

                        // sortOptions
                        if ($scope.gridOptions['useExternalSorting']) {
                            $scope.gridOptions.sortInfo = {
                                columns: [],
                                fields: [],
                                directions: []
                            };

                            $scope.$watch('gridOptions.sortInfo', function(sort, oldsort) {
                                if (sort != oldsort) {

                                    var ds = $scope.datasource;
                                    if (typeof ds != "undefined") {
                                        var order_by = [];
                                        for (i in sort.fields) {
                                            order_by.push({
                                                field: sort.fields[i],
                                                direction: sort.directions[i]
                                            });
                                        }
                                        ds.updateParam('order_by', order_by, 'order');
                                        ds.query();

                                    }
                                }
                            }, true);
                        }

                        if (typeof $scope.onGridLoaded == 'function') {
                            $scope.onGridLoaded($scope.gridOptions);
                        }

                        $scope.loaded = true;
                    }, 0);
                }

                $scope.$watch('datasource.data', function() {
                    if ($scope.datasource != null) {
                        $scope.data = $scope.datasource.data;
                    }
                });
                
                $scope.reset = function() {
                    location.reload();
                }
                
                $scope.Math = window.Math;
                $scope.grid = null;
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