app.directive('psDataTable', function ($timeout, $http, $compile, $filter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                $scope.triggerWatch = true;
                $scope.name = $el.find("data[name=name]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = $scope.$parent[$el.find("data[name=datasource]").text()];
                $scope.lastRelList = {};

                var colHeaders = [];
                var columns = [];

                // define columns
                var categories = [];
                var lastCat = '';

                for (i in $scope.columns) {
                    var c = $scope.columns[i];
                    if (c.options.visible && c.options.visible == "false") {
                        continue;
                    }
                    var colDef = {
                        data: c.name
                    };
                    switch (c.columnType) {
                        case "dropdown":
                            colDef.type = "dropdown";
                            if (c.listType == 'js') {
                                c.listItem = $scope.$parent.$eval(col.listExpr);
                            }
                            colDef.source = $scope.$parent.$eval(c.listItem);
                            break;
                        case "relation":
                            colDef.data = c.name + "_label";
                            colDef.type = "autocomplete";
                            colDef.renderer = "relation";
                            colDef.editor = "relation";
                            colDef.scope = $scope;
                            colDef.source = function (query, process) {
                                if ($scope.triggerWatch) {
                                    return false;
                                }

                                var s = this.instance.getSelected();
                                if (s) {
                                    var row = s[0];
                                    var col = s[1];
                                    var opt = this.instance.getSettings().columns[col];

                                    if (opt.columnType != "relation")
                                        return;

                                    for (i in opt.relParams) {
                                        var p = opt.relParams[i];
                                        if (p.indexOf('js:') === 0) {
                                            var value = $scope.$eval(p.replace('js:', ''));
                                            opt.relParams[i] = value;
                                        }
                                    }
                                    var ck = {
                                        's': query,
                                        'm': $scope.modelClass,
                                        'f': $scope.name,
                                        'c': opt.name,
                                        'p': opt.relParams
                                    };

                                    $http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), ck)
                                            .success(function (data) {
                                                // cache query

                                                var labels = [];
                                                for (i in data) {
                                                    labels.push(data[i].label);
                                                    $scope.lastRelList[data[i].label] = data[i].value;
                                                }
                                                if (labels.length && labels.length > 0) {
                                                    process(labels);
                                                }
                                            });
                                }
                            };
                            break;
                        case "string":
                            if (c.stringAlias) {
                                colDef.renderer = "html";
                            }
                            switch (c.inputMask) {
                                case "number":
                                    colDef.type = 'numeric';
                                    colDef.format = '0,0.00';
                                    break;
                                case "99/99/9999":
                                case "99/99/9999 99:99":
                                case "99:99":
                                    colDef.renderer = 'datetime';
                                    colDef.editor = 'mask';
                                    colDef.filter = $filter;
                                    break;
                            }
                            break;
                    }

                    if (c.options.enableCellEdit == "false" || c.options.readOnly == "true") {
                        colDef.readOnly = true;
                    }

                    var col = $.extend(c, colDef);
                    //add column
                    columns.push(col);

                    // add header
                    colHeaders.push(c.label);

                    // add category header
                    var cat = c.options.category || '';
                    if (lastCat == cat) {
                        if (categories.length == 0 && lastCat == '') {
                            categories.push({
                                title: lastCat,
                                span: 0
                            });
                        }

                        var idx = i;
                        while (typeof categories[idx] == "undefined" && idx > 0) {
                            idx--;
                        }
                        if (typeof categories[idx] != "undefined") {
                            categories[idx].span++;
                        }
                    } else {
                        categories.push({
                            title: cat,
                            span: 1
                        });
                    }
                    lastCat = cat;
                }

                // fixedHeader
                var fh = {};
                $timeout(function () {
                    fh = {
                        container: $el.parents('.container-full'),
                        dgcontainer: $el.find(".data-table-container"),
                        topp: $el.find('.data-table-container .ht_clone_top'),
                        form: $el.parents("form"),
                    }
                    fh.formTopPos = Math.abs(fh.form.position().top - fh.form.offset().top);
                    fh.formTop = fh.form.offset().top;

                });

                function fixHead() {
                    if ((fh.container.scrollTop() > fh.formTop) || $scope.gridOptions['fixedHeader'] == "always") {
                        if (!$el.hasClass('fixed')) {
                            $el.addClass('fixed');
                        }

                        fh.topp.css('top', fh.formTop)
                                .css('left', fh.dgcontainer.offset().left)
                                .height(70);
                    } else {
                        if ($el.hasClass('fixed')) {
                            $el.removeClass('fixed');
                        }
                    }
                }

                if ($scope.gridOptions['fixedHeader'] !== false) {
                    $timeout(function () {
                        $(window).resize(fixHead);
                        $el.on('mousedown', 'td', fixHead);
                        $el.parents('.container-full').scroll(fixHead);
                        fixHead();
                    }, 100);
                }
                var options = $.extend({
                    data: $scope.datasource.data,
                    minSpareRows: 1,
                    columnSorting: true,
                    contextMenu: true,
                    colHeaders: colHeaders,
                    columns: columns,
                    rowaHeaders: false,
                    manualColumnResize: true,
                    beforeAutofill: function (s, e, d) {
                        if (s.col == e.col && d.length > 1) {
                            var col = columns[s.col].data;
                            var seq = Math.abs(d[0][0] - d[1][0]);

                            if (!isNaN(seq) && seq > 0) {
                                var se = (d[d.length - 1][0] * 1);
                                for (i = s.row; i <= e.row; i++) {
                                    var row = $scope.datasource.data[i];
                                    row[col] = (se * 1) + seq;
                                    se = row[col];
                                }
                            }
                            return false;
                        }
                    },
                    beforeChange: function (changes, source) {
                        if (typeof $scope.beforeCellEdit == "function" && source == "edit") {
                            var ht = $("#" + $scope.renderID).handsontable('getInstance');
                            var ch = changes[0];
                            // beforeCellEdit(value, row, col, data, ht);
                            $scope.beforeCellEdit(ch[3], ch[0], ch[1], $scope.datasource.data[ch[0]], ht);
                        }
                    },
                    beforeKeyDown: function (event) {
                        if (typeof $scope.beforeKeyDown == "function") {
                            var ht = $("#" + $scope.renderID).handsontable('getInstance');
                            $scope.beforeKeyDown(event, ht);
                        }
                    },
                    afterChange: function (changes, source) {
                        if (typeof $scope.afterCellEdit == "function" && source == "edit") {
                            var ht = $("#" + $scope.renderID).handsontable('getInstance');
                            var ch = changes[0];
                            // afterCellEdit(value, row, col, data, ht);
                            $scope.afterCellEdit(ch[3], ch[0], ch[1], $scope.datasource.data[ch[0]], ht);
                        }
                    },
                    afterRender: function () {
                        if (categories.length > 0) {
                            //add category header
                            var html = '<tr class="header-grouping">';
                            for (i in categories) {
                                var c = categories[i];
                                html += '<th colspan="' + c.span + '"><div class="relative">&nbsp;' + c.title + '&nbsp;</div></th>';
                            }
                            html += '</tr>';
                            $el.find('.header-grouping').remove();
                            $el.find('.ht_master thead').prepend(html);
                        }

                        //fix header
                        $timeout(function () {
                            fh.topp.find('.wtSpreader').removeClass('wtSpreader')
                                    .addClass('ht_top')
                                    .addClass('handsontable')
                                    .remove()
                                    .insertAfter($el.find('.data-table-container'));
                            fh.topp = $el.find('.ht_top');

                            $el.find('.ht_top').remove();
                            $el.find('.ht_top thead').prepend(html);
                            fixHead();
                        });
                    },
                    afterLoadData: function () {
                        $timeout(function () {
                            if (!$scope.gridLoaded && typeof $scope.onGridLoaded == "function") {
                                $scope.onGridLoaded(options);
                                $scope.gridLoaded = true;
                            }
                        });
                    },
                    beforeRender: function () {
                        $el.find('.header-grouping').remove();
                    },
                    modifyColWidth: function () {
                        $el.find('.header-grouping').remove();
                    },
                    contextMenu: ['row_above', 'row_below', '---------', 'remove_row', '---------', 'undo', 'redo']
                }, $scope.gridOptions);

                if (typeof $scope.beforeGridLoaded == "function") {
                    $scope.beforeGridLoaded(options);
                }

                $("#" + $scope.renderID).handsontable(options);

                //relation init
                var dgr = {};
                var relCols = [];

                function countDgr() {
                    relCols = [];
                    for (i in columns) {
                        if (columns[i].columnType == "relation") {
                            relCols.push(columns[i]);
                        }
                    }
                    for (i in $scope.datasource.data) {
                        var d = $scope.datasource.data[i];

                        for (ir in relCols) {
                            var r = relCols[ir];
                            var model = r.relModelClass;
                            var id = d[r.name];
                            var name = $scope.name;
                            var cls = $scope.modelClass;
                            var col = r.name;
                            var labelField = r.relLabelField;
                            var idField = r.relIdField;

                            dgr['name'] = name;
                            dgr['class'] = cls;
                            dgr['cols'] = dgr['cols'] || {};
                            dgr['cols'][col] = dgr['cols'][col] || [];
                            if (id != "" && id != null && dgr['cols'][col].indexOf(id) < 0) {
                                dgr['cols'][col].push(id);
                            }
                        }
                    }
                }

                function loadRelation(callback) {
                    $scope.triggerWatch = false;
                    countDgr();
                    if (relCols.length > 0) {
                        $scope.loading = true;
                        var url = Yii.app.createUrl('/formfield/RelationField.dgrInit');
                        $http.post(url, dgr).success(function (data) {
                            for (rowIdx in $scope.datasource.data) {
                                var row = $scope.datasource.data[rowIdx];

                                for (dataIdx in data) {
                                    var d = data[dataIdx];
                                    if (row[dataIdx]) {
                                        for (i in d) {
                                            if (d[i].value == row[dataIdx]) {
                                                row[dataIdx + "_label"] = d[i].label;
                                                break;
                                            }
                                        }
                                    }
                                    if (!row[dataIdx + "_label"]) {
                                        row[dataIdx + "_label"] = '';
                                    }
                                }

                            }
                            $scope.loading = false;
                            $timeout(function () {
                                $scope.triggerWatch = true;
                                if (typeof callback == "function") {
                                    callback();
                                }
                            });
                        });
                    }
                }

                $timeout(function () {
                    loadRelation();
                });

                //$scope.$watch('datasource.data', function (n, o) {
                //    if (n !== o && $scope.triggerWatch) {
                //        loadRelation(function () {
                //            $("#" + $scope.renderID)
                //                .handsontable('getInstance')
                //                .loadData($scope.datasource.data);
                //        });
                //    }
                //}, true);

                $scope.$parent[$scope.name] = $scope;
            }
        }
    }
});