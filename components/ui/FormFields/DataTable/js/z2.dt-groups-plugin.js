Handsontable.DataTableGroups = function (settings) {
    return $.extend({
        groupCols: [],
        columns: [],
        colHeaders: [],
        scope: null,
        groupArgs: [],
        groupTree: {},
        totals: [],
        totalGroups: [],
        prepare: function () {
            var gc = this.groupCols;
            // hide grouped columns
            for (i in this.columns) {
                if (gc.indexOf(settings.columns[i].name) >= 0) {
                    var c = this.columns.splice(i, 1);
                    // remove col header too (if exist)
                    if (c[0].label == this.colHeaders[i]) {
                        this.colHeaders.splice(i, 1);
                    }
                }
            }

            return this;
        },
        prepareTotalRow: function (row) {
            function span(text, col) {
                return text;
            }

            function sum(col) {
                return 'sum_' + col;
            }
            
            var tr = this.totalRows.split(",");
            
            for (i in tr) {
                var text = tr[i].trim();
                
                this.columns[i]
            }
        },
        group: function (instance) {
            if (!instance)
                return;
            var cur;
            var cols = this.columns;
            var $scope = this.scope;
            var $timeout = this.scope.$timeout;
            this.groupArgs = [];
            this.groupTree = [];
            for (i in $scope.data) {
                $scope.data[i]['__dt_idx'] = i;
                $scope.data[i]['__dt_flg'] = 'Z';
            }

            groupTree = {};
            this.sortGroups();
            $scope.edited = false;
            var newrows = [];
            for (i in $scope.data) {
                var row = $scope.data[i];
                cur = groupTree;
                for (var lv = 0; lv < this.groupArgs.length; lv++) {
                    var lvcol = this.groupArgs[lv];
                    var isLastLevel = this.groupArgs.length - 1 == lv;
                    var header = row[lvcol];
                    if (header == '')
                        continue;
                    if (!cur[header]) {
                        cur[header] = {
                            items: {},
                            total: {}
                        }

                        var lvstr = "&nbsp;";
                        for (var ll = 1; ll <= lv; ll++) {
                            lvstr += "&nbsp;&nbsp;";
                        }
                        lvstr += ' <i class="gr fa fa-caret-down fa-lg "></i> &nbsp;&nbsp;&nbsp;';
                        if (i > 0 && newrows[newrows.length - 1]['__dt_lvl'] >= lv) {
                            var newrow = {};
                            newrow[this.columns[0].name] = '';
                            newrow['__dt_flg'] = "E";
                            newrow['__dt_idx'] = i;
                            newrow['__dt_lvl'] = lv;
                            newrows.push(newrow);
                        }

                        var newrow = {};
                        newrow[this.columns[0].name] = lvstr + header;
                        newrow['__dt_flg'] = "G";
                        newrow['__dt_idx'] = i;
                        newrow['__dt_lvl'] = lv;
                        newrows.push(newrow);
                    }
                    cur = cur[header];
                    if (isLastLevel) {
                        cur.items[i] = row;
                    }
                }
            }
            console.log(groupTree);
            this.changed = true;
            var colLength = this.columns.length;
            var cellMerge = [];
            for (var i = newrows.length - 1; i >= 0; i--) {
                $scope.data.splice(newrows[i]['__dt_idx'], 0, newrows[i]);
                var row = newrows[i]['__dt_idx'] * 1 + i;
                cellMerge.unshift({
                    row: row,
                    col: 0,
                    rowspan: 1,
                    colspan: colLength
                });
            }
            instance.mergeCells = new Handsontable.MergeCells(cellMerge);
            instance.render();
            $timeout(function () {
                this.changed = false;
            });
        },
        sortGroups: function () {
            var $scope = this.scope;
            function dynamicSort(property) {
                var sortOrder = 1;
                if (property[0] === "-") {
                    sortOrder = -1;
                    property = property.substr(1);
                }
                return function (a, b) {
                    var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
                    return result * sortOrder;
                }
            }

            function dynamicSortMultiple() {
                var props = arguments;
                return function (obj1, obj2) {
                    var i = 0, result = 0, numberOfProperties = props.length;
                    /* try getting a different result from 0 (equal)
                     * as long as we have extra properties to compare
                     */
                    while (result === 0 && i < numberOfProperties) {
                        result = dynamicSort(props[i])(obj1, obj2);
                        i++;
                    }

                    return result;
                }
            }

            this.groupArgs = [];
            for (i in this.groupCols) {
                this.groupArgs.push(this.groupCols[i]);
            }

            $scope.data.sort(dynamicSortMultiple.apply(this, this.groupArgs));
        }
    }, settings);
};