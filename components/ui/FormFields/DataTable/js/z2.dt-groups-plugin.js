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
            for (var i = this.columns.length - 1; i >= 0; i--) {
                if (gc.indexOf(this.columns[i].name) >= 0) {
                    var c = this.columns.splice(i, 1);

                    // remove col header too (if exist)
                    if (c[0].label == this.colHeaders[i]) {
                        this.colHeaders.splice(i, 1);
                    }
                }
            }

            // prepare total group
            if (this.totalGroups) {
                var tg = this.totalGroups.split(",");
                this.totalGroups = {};
                for (i in this.columns) {
                    var text = '';
                    if (tg[i]) {
                        text = tg[i].replace(/[\[\]]+/g, '').trim()
                    }

                    this.totalGroups[this.columns[i].name] = text;
                }
            }

            return this;
        },
        getTotalRow: function (grp) {
            var $scope = this.scope;
            if (grp.length > 0) {
                return $scope.data[this.getTreeData(grp).total];
            } else {
                return $scope.data[$scope.data.length - 1];
            }
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
                return cur;
            } else {
                return false;
            }
        },
        calculate: function () {
            var plugin = this;
            var $scope = this.scope;

            function span(text, col) {
                if (i == plugin.columns[0].name || column == plugin.columns[0].name) {
                    var lvstr = "&nbsp;";
                    for (var ll = 1; ll <= calc['__dt_lvl']; ll++) {
                        lvstr += "&nbsp;&nbsp;";
                    }
                    lvstr += '<i class="gr fa fa-angle-right fa-lg "></i> &nbsp;&nbsp;';
                    return lvstr + text;
                }

                return text;
            }

            function sum(col) {
                calc[col] += row[col] * 1;
                return calc[col] * 1;
            }


            for (var i in $scope.data) {
                var row = $scope.data[i];
                if (row['__dt_flg'] != 'T')
                    continue;

                for (var column in this.totalGroups) {
                    row[column] = '';
                }
            }

            //calculate each row
            var idx = 0;
            for (var i in $scope.data) {
                var row = $scope.data[i];
                var grp = angular.copy(row['__dt_grp']);
                if (row['__dt_flg'] != 'Z')
                    continue;

                //calculate each level
                for (var lv = row['__dt_lvl']; lv >= -1; lv--) {
                    var calc = this.getTotalRow(grp);


                    //calculate each column
                    for (var column in this.totalGroups) {
                        var formula = this.totalGroups[column];
                        if (formula) {
                            calc[column] = eval(formula);
                        }
                    }

                    grp.pop();
                }

                idx++;
            }
        },
        prepareTotalRow: function (row) {
            var $scope = this.scope;
            if (this.totalGroups) {
                for (i in this.columns) {
                    row[this.columns[i].name] = '';
                }
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
                            this.calcTotalRow.call(this, totalRow, changes[0][1]);
                            break;
                        }
                    }

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


                    if (header == '' || !header)
                        break;

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
                    }
                }
            }

            // mark data group as changed
            this.changed = true;
            var colLength = this.columns.length;
            var cellMerge = [];

            // Merge All
            for (var i = newrows.length - 1; i >= 0; i--) {
                $scope.data.splice(newrows[i]['__dt_idx'], 0, newrows[i]);
            }

            if (this.totalGroups) {
                // Add Total
                for (var i = $scope.data.length - 1; i >= 0; i--) {
                    if (i > 0) {
                        var row = $scope.data[i];
                        var last = $scope.data[i - 1];

                        if ((last['__dt_flg'] == "Z" && row['__dt_flg'] == "G") ||
                                i == $scope.data.length - 1) {

                            var newrow = angular.copy(last);
                            newrow['__dt_flg'] = "T";
                            this.prepareTotalRow(newrow);

                            if (i == $scope.data.length - 1) {
                                // add total in very last row
                                var lastgrp = angular.copy(last['__dt_grp']);
                                for (l in last['__dt_grp']) {
                                    var newrow = angular.copy(newrow);
                                    newrow['__dt_grp'] = angular.copy(lastgrp);
                                    newrow['__dt_lvl'] = newrow['__dt_grp'].length;
                                    $scope.data.push(newrow);
                                    lastgrp.pop();
                                }
                                var newrow = angular.copy(newrow);
                                newrow['__dt_grp'] = angular.copy(lastgrp);
                                newrow['__dt_lvl'] = newrow['__dt_grp'].length;
                                $scope.data.push(newrow);
                            } else {
                                // add total in last row of each group
                                var p = 0;
                                var lastgrp = angular.copy(last['__dt_grp']);
                                for (var l = last['__dt_lvl']; l >= row['__dt_lvl']; l--) {
                                    var newrow = angular.copy(newrow);
                                    newrow['__dt_grp'] = angular.copy(lastgrp);
                                    newrow['__dt_lvl'] = newrow['__dt_grp'].length;
                                    $scope.data.splice(i , 0, newrow);
                                    lastgrp.pop();
                                }
                            }
                        }
                    }
                }

                // Assign total row to groupTree
                for (i in $scope.data) {
                    if ($scope.data[i]['__dt_flg'] == 'T') {
                        this.getTreeData($scope.data[i]['__dt_grp']).total = i;
                    }
                }
            }

            // do cell Merge
            for (var i = $scope.data.length - 1; i >= 0; i--) {
                if (['G', 'E'].indexOf($scope.data[i]['__dt_flg']) >= 0) {
                    cellMerge.unshift({
                        row: i,
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
                plugin.calculate();
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