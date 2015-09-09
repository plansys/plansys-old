Handsontable.DataTableGroups = function (settings) {
    return $.extend({
        groupCols: [],
        groupColOpts: {},
        columns: [],
        colHeaders: [],
        colWidths: [],
        categories: [],
        scope: null,
        groupArgs: [],
        groupTree: {},
        totals: [],
        grouped: false,
        totalGroups: null,
        prepare: function () {
            if (this.grouped)
                return;

            var gc = this.groupCols;
            var $scope = this.scope;

            // hide grouped columns
            var gcCount = 0;
            for (var i = this.columns.length - 1; i >= 0; i--) {
                if (gc.indexOf(this.columns[i].name) >= 0) {
                    var c = this.columns.splice(i, 1);

                    this.groupColOpts[c[0].data] = c[0];
                    gcCount++;

                    if (c[0].options && (c[0].options.enableCellEdit === false || c[0].options.readOnly === true)) {
                        $scope.canAddRow = false;
                    }

                    // remove col header too (if exist)
                    if (c[0].label == this.colHeaders[i]) {
                        this.colHeaders.splice(i, 1);
                    }
                    this.colWidths.splice(i, 1);
                }
            }

            if (gcCount != gc.length) {
                $scope.canAddRow = false;
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
        handleChange: function ($scope, c) {
            var row = $scope.data[c[0]];

            switch (row['__dt_flg']) {
                case "Z":
                    var dsrow = row['__dt_row'];
                    if (!!$scope.datasource.data[dsrow]) {
                        $scope.datasource.data[dsrow][c[1]] = $scope.resolveChangedValue(c[0], c[1], c[3]);
                    }
                    break;
                case "G":
                    var rows = $scope.dtGroups.findRows(row);
                    var col = $scope.dtGroups.groupCols[row['__dt_lvl']];

                    var colDef = $scope.dtGroups.groupColOpts[col];
                    if (!colDef) {
                        colDef = $scope.dtGroups.groupColOpts[col + $scope.relSuffix];
                    }
                    if (colDef.columnType == "relation") {
                        if (col != c[1]) {
                            return;
                        }
                    }

                    rows.forEach(function (r) {
                        r[col] = c[3];
                        var dsrow = r['__dt_row'];
                        $scope.datasource.data[dsrow][col] = $scope.resolveChangedValue(c[0], col, c[3]);
                    });

                    $scope.ht = $scope.getInstance();
                    this.ungroup($scope.ht, false);
                    this.group($scope.ht);

                    break;
            }
        },
        ungroup: function (instance, shouldRender) {
            if (!instance || !this.grouped)
                return;

            var $scope = this.scope;
            var len = $scope.data.length;
            for (var i = len - 1; i >= 0; i--) {
                var item = $scope.data[i];
                if (item['__dt_flg'] != 'Z') {
                    $scope.data.splice(i, 1);
                } else {
                    delete item['__dt_flg'];
                    delete item['__dt_row'];
                    delete item['__dt_idx'];
                }
            }

            if (typeof shouldRender == "undefined" || shouldRender) {
                instance.render();
            }
            this.grouped = false;
        },
        group: function (instance) {
            if (!instance || !instance.render || this.grouped)
                return;

            var $scope = this.scope;
            var grouped = [];
            var groupTreeInternal = {
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
                var cur = groupTreeInternal;
                groups.forEach(function (g, gidx) {
                    var group = item[g];
                    if (typeof group == "undefined") {
                        return;
                    }

                    // add new group
                    if (!cur.groups[group]) {
                        // add footer
                        // add group row
                        var newrow = {};
                        newrow[$scope.columns[0].name] = group;
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
                item['__dt_lvl'] = grouped[group_idx - 1]['__dt_lvl'];
                grouped.splice(newidx, 0, item);
                cur.rows.push(item);
                group_idx++;
            });

            this.groupTree = groupTreeInternal;

            $scope.data.length = 0;
            grouped.forEach(function (item, idx) {
                $scope.data.push(item);
            });

            this.grouped = true;
            instance.render();
        },
        flattenRows: function (group) {
            if (!group)
                return[];

            var rows = group.rows;
            for (var i in group.groups) {
                var r = this.flattenRows(group.groups[i]);
                rows = rows.concat(r);
            }

            return rows;
        },
        flattenGroups: function(group) {
            if (!group)
                return[];

            var rows = [group.group];
            for (var i in group.groups) {
                var r = this.flattenGroups(group.groups[i]);
                rows = rows.concat(r);
            }

            return rows;
        },
        findRows: function (row) {
            var arr = this.flattenRows(this.findGroup(row));
            return $.grep(arr,function(n){ return(n) });
        },
        findGroupFlatten: function (row) {
            var arr = this.flattenGroups(this.findGroup(row));
            return $.grep(arr,function(n){ return(n) });
        },
        findGroup: function (row, cur) {
            if (typeof cur == "undefined") {
                var cur = this.groupTree;
            }
            var found = false;

            if (typeof row == "undefined") {
                return cur;
            } else {
                if (cur.group && cur.group['__dt_idx'] == row['__dt_idx']) {
                    return cur;
                }
                for (var r in cur.rows) {
                    if (cur.rows[r]['__dt_idx'] == row['__dt_idx']) {
                        return cur;
                    }
                }

                for (var i in cur.groups) {
                    var found = this.findGroup(row, cur.groups[i]);
                    if (found !== false) {
                        return found;
                    }
                }
    
                return found;
            }
        },
        addRow: function () {
            var gp = this;
            var $scope = this.scope;
            var item = {
                '__dt_flg': 'Z',
                '__dt_idx': $scope.data.length,
                '__dt_row': $scope.datasource.data.length
            };
            gp.groupCols.forEach(function (col) {
                item[col] = "";
            });

            $scope.datasource.data.splice($scope.datasource.data.length, 0, item);
            $scope.data.splice($scope.data.length, 0, item);

            this.ungroup($scope.ht, false);
            this.group($scope.ht);
        },
        contextMenu: function () {
            if (this.grouped)
                return;

            var gp = this;
            var $scope = this.scope;
            var $timeout = $scope.$timeout;

            function contextMenuShouldDisable() {
                if (!$scope.ht) {
                    $scope.ht = $scope.getInstance();
                }
                if ($scope.ht) {
                    var sel = $scope.ht.getSelected();
                    if (sel) {
                        var start = Math.min(sel[0], sel[2]);
                        var end = Math.max(sel[0], sel[2]);
                        var disabled = false;
                        for (var i = end; i >= start; i--) {
                            var d = $scope.data[i];
                            if (d['__dt_flg'] != "Z") {
                                disabled = true;
                            }
                        }
                        return disabled;
                    } else {
                        return false;
                    }
                }
                return true;
            }

            function disableInsertMenu() {
                var enableInsert = 0;
                for (var i in gp.groupCols) {
                    if (gp.groupColOpts[gp.groupCols[i]]) {
                        if (!!gp.groupColOpts[gp.groupCols[i]].options
                                && gp.groupColOpts[gp.groupCols[i]].options['enableCellEdit'] !== false
                                && gp.groupColOpts[gp.groupCols[i]].options['readOnly'] !== true) {
                            enableInsert++;
                        }
                    }
                }

                return (enableInsert != gp.groupCols.length) || contextMenuShouldDisable();
            }

            return {
                insert: {
                    name: 'Add new row',
                    callback: function (key, selection) {
                        gp.addRow();
                        $scope.ht.render();
                        $scope.ht.selectCell(
                                $scope.data.length - 1,
                                selection.start.col,
                                $scope.data.length - 1,
                                selection.end.col);
                        $scope.fixScroll();
                    },
                    disabled: disableInsertMenu
                },
                duplicate: {
                    name: 'Duplicate row',
                    callback: function (key, selection) {
                        var start = Math.min(selection.start.row, selection.end.row);
                        var end = Math.max(selection.start.row, selection.end.row);

                        for (var i = end; i >= start; i--) {
                            var d = angular.copy($scope.data[i]);
                            if (d['__dt_flg'] == "Z") {
                                if (typeof d['id'] != "undefined") {
                                    delete d['id'];
                                }

                                $scope.datasource.data.splice(d['__dt_row'], 0, d);
                                $scope.data.splice(i, 0, d);
                            }
                        }

                        $scope.dtGroups.ungroup($scope.ht, false);
                        $scope.dtGroups.group($scope.ht);

                        $scope.ht.selectCell(
                                selection.start.row + 1,
                                selection.start.col,
                                selection.start.row + 1,
                                selection.end.col, true);

                    },
                    disabled: contextMenuShouldDisable
                },
                hsep2: '---------',
                remove_row: {
                    callback: function (key, selection) {
                        var start = Math.min(selection.start.row, selection.end.row);
                        var end = Math.max(selection.start.row, selection.end.row);

                        for (var i = end; i >= start; i--) {
                            var d = $scope.data[i];
                            if (d['__dt_flg'] == "Z") {
                                $scope.datasource.data.splice(d['__dt_row'], 1);
                                $scope.data.splice(i, 1);
                            }
                        }

                        $scope.dtGroups.ungroup($scope.ht, false);
                        $scope.dtGroups.group($scope.ht);
                        $scope.ht.deselectCell();
                    },
                    disabled: contextMenuShouldDisable
                },
                hsep1: '---------',
                undo: {},
                redo: {}
            }
        }
    }, settings);
};
