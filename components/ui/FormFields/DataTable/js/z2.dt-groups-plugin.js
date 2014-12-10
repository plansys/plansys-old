Handsontable.DataTableGroups = function (settings) {
    return $.extend({
        groupCols: [],
        columns: [],
        colHeaders: [],
        scope: null,
        groupArgs: [],
        groupTree: {},
        totals: [],
        totalGroups: null,
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
        getTreeData: function (grp) {
            var cur = this.groupTree;
            for (i in grp) {
                if (cur[grp[i]]) {
                    cur = cur[grp[i]];
                } else {
                    return false;
                }
            }

            if (cur.items) {
                return cur.items;
            } else {
                return false;
            }
        },
        calcTotalRow: function (row, column) {
            var plugin = this;
            function span(text, col) {
                if (i == plugin.columns[0].name) {
                    var lvstr = "&nbsp;";
                    for (var ll = 1; ll <= row['__dt_lvl']; ll++) {
                        lvstr += "&nbsp;&nbsp;";
                    }
                    lvstr += ' <i class="gr fa fa-angle-right fa-lg "></i> &nbsp;&nbsp;&nbsp;';
                    return lvstr + text;
                }

                return text;
            }

            function sum(col) {
                var rows = plugin.getTreeData(row['__dt_grp']);
                var sum = 0;
                for (i in rows) {
                    sum += rows[i][col] * 1;
                }
                return sum;
            }

            if (!column) {
                for (i in row['__dt_clc']) {
                    var c = row['__dt_clc'][i];
                    if (c) {
                        row[i] = eval(c);
                    }
                }
            } else {
                row[column] = eval(row['__dt_clc'][column]);
            }
            return row;
        },
        prepareTotalRow: function (row) {
            var $scope = this.scope;
            var tg = this.totalGroups.split(",");
            row['__dt_clc'] = {};
            for (i in this.columns) {
                var text = '';
                if (tg[i]) {
                    text = tg[i].replace(/[\[\]]+/g, '').trim()
                }


                row['__dt_clc'][this.columns[i].name] = text;
                row[this.columns[i].name] = '';
            }
        },
        reCalcAll: function () {
            var $scope = this.scope;
            for (i in $scope.data) {
                var row = $scope.data[i];
                if (row['__dt_flg'] == 'T') {
                    this.calcTotalRow.call(this, row);
                }
            }
        },
        reCalcChanges: function (changes, source, instance) {
            if (!this.totalGroups) {
                return false;
            }

            var $scope = this.scope;
            var $timeout = this.scope.$timeout;
            switch (source) {
                case "edit":
                    var totalRow = null;
                    for (var i = changes[0][0]; i < $scope.data.length; i++) {

                        if ($scope.data[i]['__dt_flg'] == "T") {
                            totalRow = $scope.data[i];

                            break;
                        }
                    }

                    this.calcTotalRow.call(this, totalRow, changes[0][1]);
                    $timeout(function () {
                        instance.render();
                    });

                    break;
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
            for (i in $scope.data) {
                $scope.data[i]['__dt_idx'] = i;
                $scope.data[i]['__dt_flg'] = 'Z';
            }

            // sort all groups
            this.sortGroups();

            $scope.edited = false;

            this.groupTree = {};
            var newrows = [];
            var idx = 0;

            for (i in $scope.data) {
                var row = $scope.data[i];
                cur = this.groupTree;

                var grp = [];
                for (var lv = 0; lv < this.groupArgs.length; lv++) {
                    var lvcol = this.groupArgs[lv];
                    var isLastLevel = this.groupArgs.length - 1 == lv;
                    var header = row[lvcol];
                    if (header == '')
                        continue;

                    grp.push(header);

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

                            if (this.totalGroups) {
                                console.log(newrows[newrows.length - 1]['__dt_lvl'], lv);

                                var newrow = {};
                                newrow['__dt_flg'] = "T";
                                newrow['__dt_idx'] = i;
                                newrow['__dt_lvl'] = lv;
                                newrow['__dt_grp'] = newrows[newrows.length - 1]['__dt_grp'];
                                this.prepareTotalRow(newrow);
                                newrows.push(newrow);

//                                var newrow = {};
//                                newrow[this.columns[0].name] = '';
//                                newrow['__dt_flg'] = "E";
//                                newrow['__dt_idx'] = i;
//                                newrow['__dt_lvl'] = lv;
//                                newrow['__dt_grp'] = grp;
//                                newrows.push(newrow);
                            }

                            idx += 2;
                        }

                        var newrow = {};
                        newrow[this.columns[0].name] = lvstr + header;
                        newrow['__dt_flg'] = "G";
                        newrow['__dt_idx'] = i;
                        newrow['__dt_lvl'] = lv;
                        newrow['__dt_grp'] = grp;
                        newrows.push(newrow);
                        idx += 1;
                    }

                    cur = cur[header];
                    if (isLastLevel) {
                        row['__dt_lvl'] = lv;
                        row['__dt_grp'] = grp;
                        cur.items[idx++] = row;

                        if (this.totalGroups && i == $scope.data.length - 1) {
                            var newrow = {};
                            newrow['__dt_flg'] = "T";
                            newrow['__dt_idx'] = i + 1;
                            newrow['__dt_lvl'] = lv;
                            newrow['__dt_grp'] = newrows[newrows.length - 1]['__dt_grp'];
                            this.prepareTotalRow(newrow);
                            newrows.push(newrow);
                        }
                    }
                }
            }

            // mark data group as changed
            this.changed = true;
            var colLength = this.columns.length;
            var cellMerge = [];
            for (var i = newrows.length - 1; i >= 0; i--) {
                $scope.data.splice(newrows[i]['__dt_idx'], 0, newrows[i]);
                var row = newrows[i]['__dt_idx'] * 1 + i;
                if (['G', 'E'].indexOf(newrows[i]['__dt_flg']) >= 0) {
                    cellMerge.unshift({
                        row: row,
                        col: 0,
                        rowspan: 1,
                        colspan: colLength
                    });
                }
            }
            instance.mergeCells = new Handsontable.MergeCells(cellMerge);
            instance.render();
            var plugin = this;
            $timeout(function () {
                plugin.reCalcAll.call(plugin);
                instance.render();

                plugin.changed = false;
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