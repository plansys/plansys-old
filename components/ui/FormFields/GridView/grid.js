app.directive('gridView', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }
            var columnRaw = element.find("data[name=columns]:eq(0)").text();
            element.find("data[name=columns]:eq(0)").remove();

            return function ($scope, $el, attrs, ctrl) {
                // define current form field in parent scope
                $scope.parent = $scope.getParent($scope);

                // define vars
                $scope.loading = false;
                $scope.loaded = false;
                $scope.Math = window.Math;
                $scope.mode = 'full';
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]:eq(0)").text());
                $scope.columns = JSON.parse(columnRaw);
                $scope.defaultPageSize = $el.find("data[name=dpz]:eq(0)").text();
                $scope.datasource = $scope.parent[$el.find("data[name=datasource]:eq(0)").text()];
                $scope.checkMode = function () {
                    if ($el.width() < 750) {
                        $scope.mode = 'small';
                    } else {
                        $scope.mode = 'full';
                    }
                }
                $scope.url = function (a, b, c) {
                    return Yii.app.createUrl(a, b, c);
                };

                $scope.gridOptions.controlBar = $scope.gridOptions.controlBar !== 'false';
                $scope.rowClass = function (row, colName, colType) {
                    var agrStr = '';
                    if (typeof row[colName] !== "undefined" && row[colName] !== null && row[colName].toString) {
                        agrStr = row[colName].toString();
                    }

                    var rc = {
                        aggregate: row.$type == 'a' && agrStr != ''
                    };

                    rc['t-' + colType] = true;
                    rc['row-' + (row.$type || 'r')] = true;
                    rc['lv-' + (row.$level || 0)] = true;

                    return rc;
                }
                $scope.editKey = function (e) {
                    var ngModel = $(e.target).attr('ng-model');
                    if (e.target.value.length == e.target.selectionEnd) {
                        if (e.which == 40) {
                            var nextRow = $(e.target).parents("tr").next().find('textarea[ng-model="' + ngModel + '"]');
                            if (!!nextRow) {
                                nextRow.focus();
                            }
                        } else if (e.which == 39) {
                            var nextCol = $(e.target).parents("td").next().find('textarea');
                            if (!!nextCol) {
                                nextCol.focus();
                            }
                        }
                    } else if (e.target.selectionEnd == 0) {
                        if (e.which == 38) {
                            var prevRow = $(e.target).parents("tr").prev().find('textarea[ng-model="' + ngModel + '"]');
                            if (!!prevRow) {
                                prevRow.focus();
                            }
                        } else if (e.which == 37) {
                            var prevCol = $(e.target).parents("td").prev().find('textarea');
                            if (!!prevCol) {
                                prevCol.focus();
                            }
                        }
                    }
                }
                $scope.rowStateClass = function (row) {
                    if (!row.$rowState) {
                        return '';
                    } else {
                        return 'row-state-' + row.$rowState;
                    }
                }
                $scope.rowUndoState = function (row) {
                    var hash = {}, trans = {
                        insert: 'insertData',
                        edit: 'updateData',
                        remove: 'deleteData'
                    }

                    var diffData = $scope.datasource[trans[row.$rowState]];
                    if (row.$rowState == 'edit' || row.$rowState == 'remove') {
                        var pk = $scope.datasource.primaryKey;
                        for (i in diffData) {
                            if (diffData[i][pk] == row[pk]) {
                                diffData.splice(i, 1);
                                break;
                            }
                        }
                    } else {
                        var idx = diffData.indexOf(row);
                        diffData.splice(idx, 1);
                    }

                    $scope.updatePaging($scope.gridOptions.pageInfo);
                }

                $scope.removeRow = function (row) {
                    row.$rowState = 'remove';
                    $scope.datasource.deleteData.push(row);
                    $timeout(function () {
                        $scope.recalcHeaderWidth();
                    });
                }

                $scope.undoRemoveRow = function (row) {
                    switch (row.$rowState) {
                        case 'remove':
                            var idx = $scope.datasource.deleteData.indexOf(row);
                            $scope.datasource.deleteData.splice(idx, 1);
                            break;
                        case 'edit':
                            var idx = $scope.datasource.updateData.indexOf(row);
                            $scope.datasource.updateData.splice(idx, 1);
                            $scope.datasource.query();
                            break;
                    }
                    row.$rowState = '';
                    $timeout(function () {
                        $scope.recalcHeaderWidth();
                    });
                }

                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (!!ctrl) {
                        ctrl.$setViewValue($scope.value);
                    }
                };

                // page Setting
                $scope.range = function (n) {
                    return new Array(n);
                };
                $scope.scrollTop = function () {
                    $container.scrollTop($container.scrollTop() + $el.position().top - 53);
                };
                $scope.reset = function () {
                    $scope.resetPageSetting();
                    location.reload();
                }
                $scope.savePageSetting = function () {
                    if (!$scope.pageSetting) return;

                    $scope.showChangePage = false;
                    $scope.pageSetting.dataGrids = $scope.pageSetting.dataGrids || {};
                    $scope.pageSetting.dataGrids[$scope.name] = {
                        sort: $scope.gridOptions.sortInfo,
                        paging: $scope.gridOptions.pageInfo
                    };
                }
                $scope.loadPageSetting = function () {
                    var changing = false;
                    if (typeof $scope.gridOptions.pageSize != "undefined") {
                        $scope.gridOptions.pageInfo.pageSize = $scope.gridOptions.pageSize * 1;
                        $scope.updatePaging($scope.gridOptions.pageInfo, false);
                        changing = true;
                    }

                    if (!!$scope.pageSetting && !!$scope.pageSetting.dataGrids && !!$scope.pageSetting.dataGrids[$scope.name]) {
                        if (JSON.stringify($scope.gridOptions.sortInfo) != JSON.stringify($scope.pageSetting.dataGrids[$scope.name].sort)) {
                            $scope.gridOptions.sortInfo = $scope.pageSetting.dataGrids[$scope.name].sort;
                            $scope.updateSorting($scope.gridOptions.sortInfo, false);
                            changing = true;
                        }

                        if (JSON.stringify($scope.gridOptions.pageInfo) != JSON.stringify($scope.pageSetting.dataGrids[$scope.name].paging)) {
                            if (typeof $scope.pageSetting.dataGrids[$scope.name].paging != "undefined") {
                                $scope.gridOptions.pageInfo = $scope.pageSetting.dataGrids[$scope.name].paging;

                                $scope.updatePaging($scope.gridOptions.pageInfo, false);
                                changing = true;
                            }
                        }
                    }

                    if (changing) {
                        $scope.datasource.query();
                    } else {
                        $scope.datasource.trackChanges = true;
                    }
                }

                // update sorting
                $scope.sort = function (col) {
                    if (col == '') return;

                    var direction = $scope.isSort(col, 'asc') ? 'desc' : 'asc';
                    $scope.gridOptions.sortInfo = {
                        fields: [col],
                        directions: [direction]
                    };
                    $scope.updateSorting($scope.gridOptions.sortInfo);
                    $timeout(function () {
                        $scope.recalcHeaderWidth();
                    });
                }
                $scope.isSort = function (col, dir) {
                    if (!$scope.gridOptions.sortInfo) return false;

                    var idx = $scope.gridOptions.sortInfo.fields.indexOf(col);
                    if (idx >= 0) {
                        if ($scope.gridOptions.sortInfo.directions[idx] == dir) {
                            return true;
                        }
                    }
                    return false;
                }
                $scope.updateSorting = function (sort, executeQuery) {
                    var ds = $scope.datasource;
                    if (typeof ds != "undefined") {
                        var order_by = [];
                        for (i in sort.fields) {
                            if (sort.fields[i] == '') {
                                return;
                            }
                            order_by.push({
                                field: sort.fields[i],
                                direction: sort.directions[i]
                            });
                        }

                        ds.lastQueryFrom = "GridView";
                        ds.updateParam('order_by', order_by, 'order');
                        if (typeof executeQuery == "undefined") {
                            executeQuery = true;
                        }
                        if (executeQuery) {
                            ds.queryWithoutCount();
                            $scope.savePageSetting();
                        }
                    }
                }

                $scope.hideGroup = function (item, e) {
                    e.stopPropagation();
                    e.preventDefault();

                    item.$hide = !item.$hide;
                    var loop = true;
                    var cursor = $(e.target).parents('tr').next();
                    while (loop) {
                        if (cursor.attr('lv') > item.$level || (cursor.hasClass('a') && cursor.attr('lv') >= item.$level)) {
                            if (item.$hide) {
                                cursor.addClass('hide');
                            } else {
                                cursor.removeClass('hide');
                            }
                            cursor = cursor.next();
                        } else {
                            loop = false;
                        }
                    }
                    $scope.recalcHeaderWidth();
                }

                // update paging
                $scope.updatePaging = function (paging, executeQuery, oldpaging) {
                    var ds = $scope.datasource;
                    ds.updateParam('currentPage', paging.currentPage, 'paging');
                    ds.updateParam('pageSize', paging.pageSize, 'paging');
                    ds.updateParam('totalServerItems', paging.totalServerItems, 'paging');
                    ds.lastQueryFrom = "GridView";

                    paging.typingPage = paging.currentPage * 1;
                    if (typeof executeQuery == "undefined") {
                        executeQuery = true;
                    }
                    if (executeQuery) {
                        if (typeof oldpaging != "undefined" && paging.pageSize == oldpaging.pageSize) {
                            ds.queryWithoutCount();
                        } else {
                            ds.query();
                        }
                        $scope.savePageSetting();
                    }
                }

                $scope.pagingKeyPress = function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    $timeout(function () {
                        if (!$scope.loading) {
                            $scope.showChangePage = true;
                            if (e.which == 13) {
                                $scope.changePage();
                            }
                        }
                    });
                }
                $scope.changePage = function () {
                    $scope.gridOptions.pageInfo.currentPage = $scope.gridOptions.pageInfo.typingPage;
                    $scope.savePageSetting();
                }
                $scope.firstPage = function () {
                    $scope.gridOptions.pageInfo.currentPage = 1;
                    $scope.savePageSetting();
                }
                $scope.prevPage = function () {
                    $scope.gridOptions.pageInfo.currentPage -= 1;
                    if ($scope.gridOptions.pageInfo.currentPage <= 1) {
                        $scope.gridOptions.pageInfo.currentPage = 1;
                    }
                    $scope.savePageSetting();
                }
                $scope.nextPage = function () {
                    $scope.gridOptions.pageInfo.currentPage += 1;
                    if ($scope.gridOptions.pageInfo.currentPage > $scope.datasource.totalItems) {
                        $scope.gridOptions.pageInfo.currentPage = $scope.datasource.totalItems;
                    }
                    $scope.savePageSetting();
                }
                $scope.lastPage = function () {
                    $scope.gridOptions.pageInfo.currentPage = $scope.datasource.totalItems;
                    $scope.savePageSetting();
                }

                $scope.deleteRow = function (idx) {
                    $scope.datasource.data.splice(idx, 1);
                }

                // fixed header on scroll
                var $container = $el.parents('.container-full');
                var $header = $el.find("table > thead");
                var inViewport = function (el, offset) {
                    var rect = el[0].getBoundingClientRect();
                    return (
                        rect.bottom >= parseInt($container.css('margin-top')) + offset &&
                        rect.right >= 0 &&
                        rect.left < $container.width() &&
                        rect.top < 0
                    );
                }
                $scope.recalcHeaderWidth = function () {
                    var count = $(".thead .th").length;
                    $el.find(".thead .th").each(function (i) {
                        var offset = (count - 1 == i ? 1 : 0);
                        $(this).width($header.find("th:eq(" + i + ")").outerWidth() - offset);
                    });
                }
                $(window).resize(function () {
                    $timeout(function () {
                        $scope.recalcHeaderWidth();
                        $scope.checkMode();
                    }, 400);
                });

                $container.scroll(function () {
                    var $thead = $el.find(".thead");
                    var elOffset = parseInt($el.css('padding-top'));
                    var headerOffset = elOffset + $header.height();
                    var fixedHeader = inViewport($el, headerOffset);
                    if (fixedHeader) {
                        $thead.css({
                            top: $container.offset().top + 'px',
                            left: $el.offset().left + 'px'
                        });
                        $thead.addClass("show");
                    } else {
                        $thead.removeClass("show");
                    }
                }.bind($scope));

                // merge same row value
                $scope.mergeSameRowValue = function () {
                    $el.find('table tbody tr.r td.rowSpanned').removeClass('rowSpanned');
                    $el.find('table tbody tr.r td[rowspan]').removeAttr('rowspan');
                    var totalRow = $el.find('table tbody tr.r').length;

                    function mergeRow(c, el, row) {
                        var text = $(el).text();
                        if (c.$prevText === 'INITIAL-PREV-TEXT') {
                            c.$newRow = $(el);
                        }

                        if (text != c.$prevText) {
                            if (c.$totalSpan > 1) {
                                c.$newRow.attr('rowspan', c.$totalSpan);
                                c.$prevRow.forEach(function (r) {
                                    r.addClass('rowSpanned');
                                });
                            }

                            // reset new Row
                            c.$newRow = $(el);
                            c.$totalSpan = 1;
                            c.$prevRow.length = 0;
                        } else if (!$(el).parent().prev().hasClass('group') && $(el).parent().hasClass('r')) {
                            c.$prevRow.push($(el));
                            c.$totalSpan++;
                        }

                        if (totalRow - 1 == row) {
                            if (c.$totalSpan > 1) {
                                c.$newRow.attr('rowspan', c.$totalSpan);
                                c.$prevRow.forEach(function (r) {
                                    r.addClass('rowSpanned');
                                });
                            }
                        }

                        c.$prevText = text;
                        c.$rowIndex = row;
                    }

                    // loop each row to merge
                    $el.find('table tbody tr.r').each(function (rowIndex) {
                        for (var i = 0; i < $scope.columns.length; i++) {
                            var c = $scope.columns[i];
                            if (!!c.mergeSameRow && c.mergeSameRow == 'Yes') {
                                if (rowIndex == 0) {
                                    c.$newRow = null;
                                    c.$prevRow = [];
                                    c.$prevText = 'INITIAL-PREV-TEXT';
                                    c.$totalSpan = 1;
                                }

                                // if mergeWith != none
                                if (!!c.mergeSameRowWith && c.mergeSameRowWith != c.name) {
                                    if (!c.$anchorCol) {
                                        $scope.columns.forEach(function (e, ei) {
                                            if (e.name === c.mergeSameRowWith && !!e.$prevText) {
                                                c.$anchorCol = e;
                                                c.$anchorIdx = ei;
                                            }
                                        }.bind(c));
                                    }

                                    if (!!c.$anchorCol && c.$anchorCol.mergeSameRow == 'Yes') {
                                        if (c.$anchorCol.$rowIndex != rowIndex) {
                                            mergeRow(c.$anchorCol, $(this).find("td").eq(c.$anchorIdx), c.$anchorIdx);
                                        }

                                        var el = $(this).find("td").eq(i);
                                        if (c.$anchorCol.$totalSpan > 1) {
                                            mergeRow(c, el, rowIndex);
                                        } else {
                                            if (c.$totalSpan > 1) {
                                                c.$newRow.attr('rowspan', c.$totalSpan);
                                                c.$prevRow.forEach(function (r) {
                                                    r.addClass('rowSpanned');
                                                });
                                            }

                                            //reset row
                                            var text = $(el).text();
                                            c.$prevText = text;
                                            c.$rowIndex = rowIndex;
                                            c.$newRow = $(el);
                                            c.$totalSpan = 1;
                                        }
                                    }
                                }

                                // if mergeWith == none (AND it is not merged yet)
                                else if (c.$rowIndex != rowIndex) {
                                    mergeRow(c, $(this).find("td").eq(i), rowIndex);
                                }
                            }
                        }
                    });

                    $scope.cleanRow();
                }

                $scope.cleanRow = function () {
                    $el.find('table tbody tr.a .rowSpanned').removeClass('rowSpanned').removeAttr('rowspan');
                    $el.find('table tbody tr.r.hide').removeClass('hide');
                }

                // checkbox handling
                $scope.checkbox = {};
                $scope.getModifyDS = function (col) {
                    if (!!col.options && !!col.options.modifyDataSource && col.options.modifyDataSource == 'false') {
                        return false;
                    }
                    return true;
                }
                $scope.checkboxValues = function (colName, column) {
                    var ret = [];
                    for (i in $scope.checkbox[colName]) {
                        if (typeof $scope.checkbox[colName][i][column] != "undefined") {
                            ret.push($scope.checkbox[colName][i][column])
                        }
                    }
                    return ret.join(",");
                }
                $scope.checkboxRow = function (row, colName, colIdx, e) {
                    var modify = $scope.getModifyDS($scope.columns[colIdx]);
                    if (typeof $scope.checkbox[colName] == "undefined") {
                        $scope.checkbox[colName] = [];
                    }
                    var isChecked = $(e.target).is(":checked");
                    var rowFound = -1;
                    for (a in $scope.checkbox[colName]) {
                        if ($scope.checkbox[colName][a][$scope.datasource.primaryKey] == row[$scope.datasource.primaryKey]) {
                            rowFound = a;
                        }
                    }
                    if (isChecked) {
                        if (rowFound < 0) {
                            $scope.checkbox[colName].push(row);
                        }
                        if (modify) {
                            row[colName] = $scope.columns[colIdx].checkedValue;
                        }
                    } else {
                        if (rowFound >= 0) {
                            $scope.checkbox[colName].splice(rowFound, 1);
                        }
                        if (modify) {
                            row[colName] = $scope.columns[colIdx].uncheckedValue;
                        }
                    }
                }
                $scope.checkboxGroup = function (rowIdx, colName, colIdx, e) {
                    var loop = true;
                    var modify = $scope.getModifyDS($scope.columns[colIdx]);
                    var cursor = $(e.target).parents("tr").next();
                    var level = (rowIdx == -1 ? -1 : $scope.datasource.data[rowIdx].$level);
                    if (level < 0) {
                        cursor = $el.find("table tbody tr:eq(0)");
                    }

                    var isChecked = $(e.target).is(":checked");
                    while (loop) {
                        var row = $scope.datasource.data[++rowIdx];
                        if (typeof $scope.checkbox[colName] == "undefined") {
                            $scope.checkbox[colName] = [];
                        }
                        if (cursor.attr("lv") > level) {
                            if (cursor.hasClass("r")) {
                                var rowFound = -1;
                                for (a in $scope.checkbox[colName]) {
                                    if ($scope.checkbox[colName][a][$scope.datasource.primaryKey] == row[$scope.datasource.primaryKey]) {
                                        rowFound = a;
                                    }
                                }
                                if (isChecked) {
                                    if (rowFound < 0) {
                                        $scope.checkbox[colName].push(row);
                                    }
                                    if (modify) {
                                        row[colName] = $scope.columns[colIdx].checkedValue;
                                    }
                                } else {
                                    if (rowFound >= 0) {
                                        $scope.checkbox[colName].splice(rowFound, 1);
                                    }
                                    if (modify) {
                                        row[colName] = $scope.columns[colIdx].uncheckedValue;
                                    }
                                }
                            } else if (cursor.hasClass("g")) {
                                cursor.find(".cb-" + colName).prop('checked', isChecked);
                            }
                        } else {
                            loop = false;
                        }
                        cursor = cursor.next();
                        if (cursor.length == 0) {
                            loop = false;
                        }
                    }
                }
                $scope.checkboxAll = function (colName, colIdx, e) {
                    $scope.checkboxGroup(-1, colName, colIdx, e);
                }
                $scope.checkboxRowChecked = function (row, colName, colIdx) {
                    if (!!$scope.getModifyDS($scope.columns[colIdx])) {
                        if (row[colName] == $scope.columns[colIdx].checkedValue) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        if (!$scope.checkbox[colName]) return false;
                        for (a in $scope.checkbox[colName]) {
                            if ($scope.checkbox[colName][a][$scope.datasource.primaryKey] == row[$scope.datasource.primaryKey]) {
                                return true;
                            }
                        }
                        return false;

                    }
                }

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    // if ngModel is present, use that instead of value from php
                    if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                        return;

                    if (typeof ctrl.$viewValue != "undefined") {
                        $scope.value = ctrl.$viewValue;
                        $scope.update();
                    }
                }

                // initialize gridView
                $scope.initGrid = function () {
                    $scope.gridOptions.pageInfo = {
                        pageSizes: [10, 25, 50, 100, 250, 500, 1000],
                        pageSize: $scope.defaultPageSize,
                        totalServerItems: $scope.datasource.totalItems,
                        currentPage: 1,
                        typingPage: 1
                    };

                    if (typeof $scope.gridOptions.pageSize != "undefined") {
                        $scope.gridOptions.pageInfo.pageSize = $scope.gridOptions.pageSize * 1;
                    }

                    $scope.$watch('gridOptions.pageInfo', function (paging, oldpaging) {
                        if (paging.typingPage != oldpaging.typingPage) return;
                        if (paging != oldpaging) {
                            var maxPage = Math.ceil($scope.datasource.totalItems / $scope.gridOptions.pageInfo.pageSize);

                            if (isNaN($scope.gridOptions.pageInfo.currentPage) || $scope.gridOptions.pageInfo.currentPage == '') {
                                $scope.gridOptions.pageInfo.currentPage = 1;
                            }

                            if ($scope.gridOptions.pageInfo.currentPage > maxPage) {
                                $scope.gridOptions.pageInfo.currentPage = maxPage;
                            }

                            if (typeof $scope.datasource != "undefined" && typeof paging != "undefined") {
                                if ($scope.pagingTimeout != null) {
                                    clearTimeout($scope.pagingTimeout);
                                }
                                $scope.pagingTimeout = setTimeout(function () {
                                    $scope.updatePaging(paging, true, oldpaging);
                                }, 100);
                            }
                        }
                    }, true);
                    $timeout(function () {
                        if (!$scope.loaded && !$scope.loading) {
                            $scope.loaded = true;
                            $scope.onGridRender('timeout');
                        }
                    }, 500);
                    $timeout(function () {
                        $scope.datasource.beforeQueryInternal[$scope.renderID] = function () {
                            $scope.loading = true;
                            if ($scope.datasource.lastQueryFrom == "DataFilter" && !!$scope.gridOptions.pageInfo) {
                                $scope.gridOptions.pageInfo.currentPage = 1;
                            }
                            $scope.datasource.trackChanges = false;
                        }
                        $scope.datasource.afterQueryInternal[$scope.renderID] = function () {
                            $scope.loading = false;
                            if (!$scope.loaded) {
                                $scope.loaded = true;
                            }
                            if (!$scope.datasource.trackChanges) {
                                $scope.datasource.resetOriginal();
                                $scope.datasource.trackChanges = true;
                            }
                            $scope.onGridRender('query');
                        };
                        $scope.loadPageSetting();
                    });
                };
                $scope.datasource.trackChanges = false;
                $scope.gridRenderTimeout = null;
                $scope.onGridRender = function (flag) {
                    if ($scope.gridRenderTimeout !== null) {
                        return;
                    }

                    $scope.gridRenderTimeout = $timeout(function () {
                        $scope.mergeSameRowValue();
                        $timeout(function () {
                            $scope.recalcHeaderWidth();
                            $scope.gridRenderTimeout = null;
                        });
                    }, 100);
                };

                $scope.initGrid();
                $scope.parent[$scope.name] = $scope;
            };
        }
    }
});