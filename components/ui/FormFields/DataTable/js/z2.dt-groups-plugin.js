Handsontable.DataTableGroups = function (settings) {
    return $.extend({
        groupCols: [],
        columns: [],
        colHeaders: [],
        scope: null,
        groupArgs: [],
        groupTree: {},
        totals: [],
        grouped: false,
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
            if (!!this.totalGroups) {
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

                    var lvstr = " ";
                    for (var ll = 1; ll <= calc['__dt_lvl']; ll++) {
                        lvstr += " ";
                    }
                    lvstr += '<i class="gr fa fa-angle-right fa-lg "></i> &nbsp;&nbsp;';
                    return lvstr + text;
                }

                return text;
            }

            function sum(col) {
                if (isNaN(parseFloat(row[col]))) {
                    return calc[col];
                }

                calc[col] += parseFloat(row[col]);
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
            if (!!this.totalGroups) {
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
        ungroup: function (instance) {
            if (!instance || !this.grouped)
                return;

            var $scope = this.scope;
            instance.mergeCells = new Handsontable.MergeCells([]);
            instance.render();

            $scope.data.forEach(function (item, idx) {
                if (item['__dt_flg'] != 'Z') {
                    $scope.data.splice(idx, i);
                }
            });

            this.grouped = false;
        },
        group: function (instance) {
            if (!instance || !instance.render || this.grouped)
                return;

            var cols = this.columns;
            var $scope = this.scope;
            var $timeout = this.scope.$timeout;
            var grouped = [];
            var cellMerge = [];
            var groupTree = {
                groups: {},
                rows: []
            };

            // generate group cols
            var groups = [];
            for (i in this.groupCols) {
                groups.push(this.groupCols[i]);
            }

            // group current data
            var group_idx = 0;
            var row_idx = 0;
            $scope.data.forEach(function (item, idx) {
                // generate groups
                var cur = groupTree;
                groups.forEach(function (g, gidx) {
                    var group = item[g];
                    if (typeof group == "undefined") {
                        return;
                    }
                    if (!cur.groups[group]) {
                        // add new group
                        var lvstr = "";
                        for (var ll = 0; ll < gidx; ll++) {
                            lvstr += "    ";
                        }
                        lvstr += '◢  ';
                        var newrow = {};
                        newrow[$scope.columns[0].name] = lvstr + group;
                        newrow[$scope.columns[0].name + "_label"] = lvstr + group;
                        newrow['__dt_flg'] = "G";
                        newrow['__dt_idx'] = group_idx++;
                        newrow['__dt_lvl'] = gidx;
                        grouped.push(newrow);

                        cur.groups[group] = {
                            group: newrow,
                            groups: {},
                            rows: []
                        };
                    }
                    cur = cur.groups[group];
                });

                if (typeof cur.group == "undefined") {
                    return;
                }

                // find new idx
                var newidx = cur.group['__dt_idx'] + 1;
                if (cur.rows.length > 0) {
                    newidx = cur.rows[cur.rows.length - 1]['__dt_idx'] + 1;
                }

                // adjust idx 
                for (i = newidx; i < grouped.length; i++) {
                    grouped[i]['__dt_idx']++;
                }

                // add row
                item['__dt_flg'] = 'Z';
                item['__dt_idx'] = newidx;
                item['__dt_row'] = row_idx++;
                grouped.splice(newidx, 0, item);
                cur.rows.push(item);
                group_idx++;
            });

            $scope.data.length = 0;
            grouped.forEach(function (item, idx) {
                $scope.data.push(item);
            });

            // do cell Merge
//            for (var i = $scope.data.length - 1; i >= 0; i--) {
//                if (['G', 'E'].indexOf($scope.data[i]['__dt_flg']) >= 0) {
//                    cellMerge.unshift({
//                        row: i,
//                        col: 0,
//                        rowspan: 1,
//                        colspan: $scope.columns.length
//                    });
//                }
//            }
//
//            instance.mergeCells = new Handsontable.MergeCells(cellMerge);
            this.grouped = true;
            instance.render();
        },
    }, settings);
};