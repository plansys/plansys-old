app.directive('psDataGrid', function ($timeout, $http, $compile, dateFilter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {

            return function ($scope, $el, attrs, ctrl) {
                function evalArray(array) {
                    for (i in array) {
                        if (typeof array[i] == "string") {
                            if (array[i].trim().substr(0, 3) == "js:") {
                                eval('array[i] = ' + array[i].trim().substr(3));
                            } else if (array[i].trim().substr(0, 4) == "url:") {
                                var url = array[i].trim().substr(4);
                                array[i] = function (row) {
                                    location.href = eval($scope.generateUrl(url, 'function'));
                                }
                            } else {
                                if (array[i].match(/true/i)) {
                                    array[i] = true;
                                } else if (array[i].match(/false/i)) {
                                    array[i] = false;
                                }
                            }
                        }
                    }
                }

                $('body').on({
                    mouseover: function () {
                        var $container = $(this);
                        $('.ngRow > .ngCellButtonCollapsedDetail').each(function () {
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
                            mouseout: function () {
                                $(this).hide().remove().appendTo($container);
                            }
                        });

                    },
                }, '.ngCellButtonCollapsed');

                $scope.pagingKeypress = function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }

                $scope.excelModeSelectedRow = null;
                $scope.excelModeSelChange = function (row, event) {
                    $scope.excelModeSelectedRow = row;
                }

                $scope.removeRow = function (row) {
                    if (typeof row == "undefined" || typeof row.rowIndex != 'number') {
                        return;
                    }

                    var index = row.rowIndex;
                    $scope.data.splice(index, 1);
                    $timeout(function () {
                        if ($scope.data.length <= index) {
                            $scope.grid.selectedItems.length = 0;
                        } else {
                            $scope.grid.gridOptions.selectItem(index, true);
                        }
                    }, 0);
                };

                $scope.isNotEmpty = function (data, except) {
                    var except = except || [];
                    var valid = false;
                    for (i in data) {
                        if (except.indexOf(i) >= 0) {
                            continue;
                        }

                        if (data[i] != "") {
                            valid = true;
                        }
                    }
                    return valid;
                }

                $scope.addRow = function (row) {

                    var data = {};
                    for (i in $scope.columns) {
                        data[$scope.columns[i].name] = '';
                    }

                    if (typeof row != "undefined" && row != null && typeof row.rowIndex == 'number') {
                        $scope.data.splice(row.rowIndex + 1, 0, data);
                    } else {
                        $scope.data.push(data);
                    }
                }

                $scope.buttonClick = function (row, e) {
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
                                    .success(function (data) {
                                        $scope.$eval($btn.attr('ajax-success'), {row: row, data: data});
                                    })
                                    .error(function (data) {
                                        $scope.$eval($btn.attr('ajax-failed'), {row: row, data: data});
                                    });

                        }

                        e.preventDefault();
                        e.stopPropagation();

                        return false;
                    }
                }

                $scope.generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {
                        if (url.match(/http*/ig)) {
                            output = url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'");
                        } else if (url.trim() == '#') {
                            output = '#';
                        } else {
                            url = url.replace(/\?/ig, '&');
                            output = "Yii.app.createUrl('" + url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'") + "')";
                        }

                        if (type == 'html') {
                            if (output != '#') {
                                output = '{{' + output + '}}';
                            }
                        }

                    }
                    return output;
                }

                $scope.generateButtons = function (column) {
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
                        var url = $scope.generateUrl(b.url, 'html');

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

                $scope.initGrid = function () {
                    $scope.grid = this;
                }

                $scope.fillColumns = function () {
                    $timeout(function () {
                        var columns = [];
                        $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];

                        if (typeof $scope.datasource != "undefined") {
                            $scope.data = $scope.datasource.data;
                        } else {
                            $scope.data = [];
                        }

                        // prepare gridOptions
                        evalArray($scope.gridOptions);
                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin(), new anchorLastColumn()];
                        $scope.gridOptions.headerRowHeight = 28;
                        $scope.gridOptions.rowHeight = 28;
                        $scope.gridOptions.multiSelect = $scope.gridOptions.multiSelect || false;

                        if ($scope.data !== null && $scope.columns !== null &&
                                $scope.data.length > 0 && $scope.columns.length == 0) {
                            for (i in $scope.data[0]) {
                                $scope.columns.push({
                                    label: i,
                                    name: i,
                                    options: {}
                                });
                            }
                        }
                        if (typeof $scope.onBeforeLoaded == 'function') {
                            $scope.onBeforeLoaded($scope);
                        }

                        // prepare ng-grid columnDefs
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
                        if (columns.length > 0) {
                            $scope.gridOptions.columnDefs = columns;
                        }


                        // pagingOptions
                        if ($scope.gridOptions['enablePaging']) {
                            $scope.gridOptions.pagingOptions = {
                                pageSizes: [25, 50, 100],
                                pageSize: 25,
                                totalServerItems: $scope.datasource.totalItems,
                                currentPage: 1
                            };
                            $scope.$watch('gridOptions.pagingOptions', function (paging, oldpaging) {
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

                            $scope.$watch('gridOptions.sortInfo', function (sort, oldsort) {
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

                        // fixedHeader
                        if ($scope.gridOptions['fixedHeader'] !== false) {
                            $timeout(function () {

                                var $container = $el.parents('.container-full');
                                var $dgcontainer = $el.find(".data-grid-container");
                                var $pager = $el.find(".data-grid-paging");
                                var $cat = $el.find('.data-grid-category');
                                var $topp = $el.find('.data-grid-table .ngTopPanel');
                                var $container = $el.parents('.container-full');
                                var $wc = $el.parent();
                                var formTop = $el.parents("form").offset().top;
                                var pagerTop = $pager.offset().top;
                                var top = pagerTop - formTop;
                                function fixHead() {
                                    var width = $wc.width();

                                    if ($container.scrollTop() > top) {
                                        if (!$dgcontainer.hasClass('fixed')) {
                                            $dgcontainer.addClass('fixed');
                                        }
                                        $pager.width(width);
                                        $pager.css('top', formTop);

                                        $cat.width(width);
                                        $cat.css('top', formTop + $pager.height() + 10);

                                        $topp.width(width);
                                        $topp.css('top', formTop + $pager.height() + $cat.height() + 10);

                                        $el.find(".data-grid-paging-shadow").show();
                                    } else {
                                        if ($dgcontainer.hasClass('fixed')) {
                                            $dgcontainer.removeClass('fixed');
                                        }
                                        $pager.attr('style', '');
                                        $cat.attr("style", '');
                                        $topp.attr("style", '');
                                        $el.find(".data-grid-paging-shadow").hide();

                                    }
                                }

                                $(window).resize(fixHead);
                                $container.scroll(fixHead);
                                fixHead();
                            }, 0);
                        }


                        // excelMode
                        if ($scope.gridOptions['enableExcelMode']) {
                            $scope.gridOptions['enableCellEdit'] = true;
                            $scope.gridOptions['enableCellSelection'] = true;
                            $scope.gridOptions['afterSelectionChange'] = $scope.excelModeSelChange;
                            $scope.lastFocus = null;


                            var excludeColumns = function (data) {
                                var except = [];
                                var cols = [];

                                for (i in $scope.columns) {
                                    cols.push($scope.columns[i].name);
                                }

                                for (i in data) {
                                    if (cols.indexOf(i) < 0) {
                                        except.push(i);
                                    }
                                }
                                return except;
                            };

                            $(window).on('focus', function () {
                                if ($scope.lastFocus != null) {
                                    $scope.lastFocus.focus();
                                }
                            });

                            $el.parents('form').submit(function (e) {
                                if ($scope.data.length > 0 || !$el.attr('gridReadyToSubmit')) {
                                    var except = excludeColumns($scope.data[0]);
                                    var newData = [];
                                    var idx = 0;
                                    for (i in $scope.data) {
                                        var row = $scope.data[i];
                                        if ($scope.isNotEmpty(row, except)) {
                                            newData.push(row);
                                        }
                                        idx++;
                                    }
                                    
                                    $scope.$apply(function () {
                                        $scope.datasource.data = newData;
                                    });
                                }
                            });

                            $el.on('focus', '[ng-cell] div', function () {
                                $scope.lastFocus = $(this);
                            });

                            $scope.$on('ngGridEventEndCellEdit', function (evt) {
                                var row = evt.targetScope.row;
                                var data = row.entity;
                                var except = excludeColumns(data);

                                if ($scope.isNotEmpty(data, except)) {
                                    if ($scope.data.length - 1 == row.rowIndex) {
                                        $timeout(function () {
                                            $scope.addRow(row);
                                        }, 0);
                                    }
                                }
                            });
                            
                            if ($scope.data.length == 0) {
                                $scope.addRow();
                            }
                        } 


                        if (typeof $scope.onGridLoaded == 'function') {
                            $scope.onGridLoaded($scope.gridOptions);
                        }

                        $scope.loaded = true;
                    }, 0);
                }

                $scope.$watch('datasource.data', function () {
                    if ($scope.datasource != null) {
                        $scope.data = $scope.datasource.data;
                    }
                });

                $scope.reset = function () {
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