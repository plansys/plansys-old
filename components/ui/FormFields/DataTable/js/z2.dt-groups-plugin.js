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

            this.grouped = true;
            instance.render();
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
                item[col] = "NEW";
            });
            $scope.datasource.data.splice($scope.datasource.data.length, 0, item);
            $scope.data.splice($scope.data.length, 0, item);
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
                }
                return true;
            }

            return {
                items: {
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
                        disabled: contextMenuShouldDisable
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

                            $scope.ht.selectCell(
                                    selection.start.row + 1,
                                    selection.start.col,
                                    selection.start.row + 1,
                                    selection.end.col, true);
                            $scope.ht.render();
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
                            $scope.ht.deselectCell();
                            $scope.ht.render();
                        },
                        disabled: contextMenuShouldDisable
                    },
                    hsep1: '---------',
                    undo: {},
                    redo: {}
                }
            }
        }
    }, settings);
};