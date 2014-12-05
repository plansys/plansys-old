app.directive('psDataTable', function ($timeout, $http, $compile, $filter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.$parent;

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

                $scope.eventsInternal = {};
                $scope.grid = function (command) {
                    command = command || 'getInstance';
                    return $("#" + $scope.renderID).handsontable(command);
                };
                $scope.edited = false;
                $scope.triggerWatch = true;
                $scope.name = $el.find("data[name=name]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = parent[$el.find("data[name=datasource]").text()];

                $scope.lastRelList = {};

                var colHeaders = [];
                var columns = [];

                // define columns
                var categories = [];
                var lastCat = '';

                for (i in $scope.columns) {
                    var c = $scope.columns[i];
                    if (c.options && c.options.visible && c.options.visible == "false") {
                        continue;
                    }
                    var colDef = {
                        data: c.name
                    };
                    switch (c.columnType) {
                        case "dropdown":
                            colDef.type = "dropdown";
                            if (c.listType == 'js') {
                                c.listItem = parent.$eval(col.listExpr);
                            }
                            colDef.source = parent.$eval(c.listItem);
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

                    if (c.options && (c.options.enableCellEdit == "false" || c.options.readOnly == "true")) {
                        colDef.readOnly = true;
                    }

                    var col = $.extend(c, colDef);
                    //add column
                    columns.push(col);

                    // add header
                    colHeaders.push(c.label);

                    if (c.options && c.options.category) {
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
                    } else {
                        if (lastCat == '' && categories.length >= 1) {
                            var lastSpan = categories[categories.length - 1];
                            lastSpan.span++;
                        } else {
                            categories.push({
                                title: '',
                                span: 1
                            });
                        }
                        lastCat = '';
                    }
                }

                if (categories.length == 1) {
                    categories.length = 0;
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
                $timeout(function () {
                    evalArray($scope.gridOptions);
                    var options = $.extend({
                        data: $scope.datasource.data,
                        minSpareRows: 1,
                        columnSorting: true,
                        contextMenu: true,
                        colHeaders: colHeaders,
                        columns: columns,
                        manualColumnResize: true,
                        beforeAutofill: function (s, e, d) {
                            if (typeof $scope.eventsInternal.beforeAutofill == "function") {
                                return $scope.eventsInternal.beforeAutofill(s, e, d);
                            }

                            if (s.col == e.col && d.length > 1) {
                                var ht = $("#" + $scope.renderID).handsontable('getInstance');
                                var col = columns[s.col].data;
                                var seq = Math.abs(d[0][0] - d[1][0]);

                                if (!isNaN(seq) && seq > 0) {
                                    var se = (d[d.length - 1][0] * 1);
                                    $scope.edited = true;
                                    $timeout(function () {
                                        for (i = s.row; i <= e.row; i++) {
                                            $scope.datasource.data[i][col] = (se * 1) + seq;
                                            se = $scope.datasource.data[i][col];
                                        }
                                        $scope.edited = false;
                                        ht.render();
                                    });
                                }
                                return false;
                            }

                        },
                        beforeChange: function (changes, source) {
                            $scope.edited = true;
                            if (typeof $scope.beforeCellEdit == "function" && source == "edit") {
                                var ht = $("#" + $scope.renderID).handsontable('getInstance');
                                var ch = changes[0];

                                // beforeCellEdit(value, row, col, data, ht);
                                $scope.beforeCellEdit(ch[3], ch[0], ch[1], $scope.datasource.data[ch[0]], ht);
                            }

                            if (typeof $scope.eventsInternal.beforeChange == "function") {
                                $scope.eventsInternal.beforeChange(changes, source);
                            }
                        },
                        beforeKeyDown: function (event) {
                            if (typeof $scope.eventsInternal.beforeKeyDown == "function") {
                                $scope.eventsInternal.beforeKeyDown(events);
                            }
                        },
                        afterChange: function (changes, source) {
                            if (typeof $scope.afterCellEdit == "function" && source == "edit") {
                                var ht = $("#" + $scope.renderID).handsontable('getInstance');
                                var ch = changes[0];
                                // afterCellEdit(value, row, col, data, ht);
                                $scope.afterCellEdit(ch[3], ch[0], ch[1], $scope.datasource.data[ch[0]], ht);
                            }

                            if (typeof $scope.eventsInternal.afterChange == "function") {
                                $scope.eventsInternal.afterChange(changes, source, $scope.grid());
                            }
                            $timeout(function () {
                                $scope.edited = false;
                            });
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
                            if (typeof $scope.eventsInternal.afterRender == "function") {
                                $scope.eventsInternal.afterRender();
                            }
                        },
                        afterLoadData: function () {
                            $timeout(function () {
                                if (!$scope.gridLoaded && typeof $scope.onGridLoaded == "function") {
                                    $scope.onGridLoaded(options);
                                    $scope.gridLoaded = true;
                                }
                            });

                            if (typeof $scope.eventsInternal.afterLoadData == "function") {
                                $scope.eventsInternal.afterLoadData();
                            }
                        },
                        beforeRender: function () {
                            $el.find('.header-grouping').remove();

                            var ht = $("#" + $scope.renderID).handsontable('getInstance');

                            for (i in $scope.columns) {
                                var c = $scope.columns[i];
                                if (c.options && c.options.width) {
                                    ht.setCellMeta(0, i, 'width', c.options.width);
                                }
                            }


                            if (typeof $scope.eventsInternal.beforeRender == "function") {
                                $scope.eventsInternal.beforeRender();
                            }
                        },
                        modifyColWidth: function () {
                            $el.find('.header-grouping').remove();

                            if (typeof $scope.eventsInternal.modifyColWidth == "function") {
                                $scope.eventsInternal.modifyColWidth();
                            }
                        },
                        contextMenu: ['row_above', 'row_below', '---------', 'remove_row', '---------', 'undo', 'redo']
                    }, $scope.gridOptions);

                    if (typeof $scope.beforeGridLoaded == "function") {
                        $scope.beforeGridLoaded(options);
                    }

                    $timeout(function () {
                        if (options.events) {
                            $scope.eventsInternal = options.events;
                        }

                        $("#" + $scope.renderID).handsontable(options);
                        $scope.ht = $("#" + $scope.renderID).handsontable('getInstance');

                        $scope.$watch('datasource.data', function (e, t) {
                            if (e !== t && !$scope.edited) {
                                $timeout(function () {
                                    $scope.ht.loadData($scope.datasource.data);
                                });
                            }
                        }, true);
                    });
                });

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

                parent[$scope.name] = $scope;
            }
        }
    }
});