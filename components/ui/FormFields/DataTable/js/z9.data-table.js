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


                $scope.generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {

                        var match = url.match(/{([^}]+)}/g);
                        for (i in match) {
                            var m = match[i];
                            m = m.substr(1, m.length - 2);
                            var result = "' + row['" + m + "'] + '";
                            if (m.indexOf('.') > 0) {
                                result = $scope.$eval(m);
                            }
                            url = url.replace('{' + m + '}', result);
                        }

                        if (url.match(/http*/ig)) {
                            output = url.replace(/\{/g, "'+ row['").replace(/\}/g, "'] +'");
                        } else if (url.trim() == '#') {
                            output = '#';
                        } else {
                            url = url.replace(/\?/ig, '&');
                            output = "Yii.app.createUrl('" + url + "')";
                        }

                        if (type == 'html') {
                            if (output != '#') {
                                output = '{{' + output + '}}';
                            }
                        }

                    }
                    return output;
                }

                // setup variables
                $scope.events = {};
                $scope.grid = function (command) {
                    command = command || 'getInstance';
                    return $("#" + $scope.renderID).handsontable(command);
                };
                $scope.edited = false;
                $scope.loadingRelation = false;
                $scope.triggerRelationWatch = true;
                $scope.name = $el.find("data[name=name]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = parent[$el.find("data[name=datasource]").text()];
                $scope.data = null;
                $scope.lastRelList = {};
                $scope.dtGroups = null;
                $scope.getInstance = function () {
                    return $("#" + $scope.renderID).handsontable('getInstance');
                }

                $scope.contextMenu = function () {
                    if ($scope.dtGroups) {
                        return [
                            'undo',
                            'redo'
                        ]
                    } else {
                        return [
                            'row_above',
                            'row_below',
                            '---------',
                            'remove_row',
                            '---------',
                            'undo',
                            'redo'
                        ];
                    }
                }

                $scope.$timeout = $timeout;

                // setup internal variables
                var colHeaders = [];
                var columnsInternal = [];
                var loadTimeout = null;
                var renderTimeout = null;
                var categories = [];
                var lastCat = '';

                // assemble each columns -- start
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
                                if (!$scope.triggerRelationWatch) {
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
                                    delete(colDef.renderer);
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
                    columnsInternal.push(col);

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
                // assemble each columns -- end

                // fixedHeader
                //TODO: still broken, fix this
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

                //relation init
                var dgr = {};
                var relCols = [];
                function countDgr() {
                    relCols = [];
                    for (i in columnsInternal) {
                        if (columnsInternal[i].columnType == "relation") {
                            relCols.push(columnsInternal[i]);
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

                // Load Relation -- start
                function loadRelation(callback) {
                    $scope.triggerRelationWatch = false;
                    countDgr();
                    if (relCols.length > 0 && dgr.name) {
                        $scope.loadingRelation = true;
                        var url = Yii.app.createUrl('/formfield/RelationField.dgrInit');
                        $http.post(url, dgr).success(function (data) {
                            for (rowIdx in $scope.data) {
                                var row = $scope.data[rowIdx];

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
                            $timeout(function () {
                                $scope.triggerRelationWatch = true;
                                if (typeof callback == "function") {
                                    callback();
                                }

                                $timeout(function () {
                                    $scope.loadingRelation = false;
                                });
                            });
                        });
                    } else {
                        $timeout(function () {
                            $scope.triggerRelationWatch = true;
                            if (typeof callback == "function") {
                                callback();
                            }

                            $timeout(function () {
                                $scope.loadingRelation = false;
                            });
                        });
                    }
                }
                // Load Relation -- end

                // Prepare Data
                function prepareData(callback) {
                    $scope.data = angular.copy($scope.datasource.data);
                    for (i in $scope.data) {
                        for (b in $scope.columns) {
                            if ($scope.columns[b].name && !$scope.data[i][$scope.columns[b].name]) {
                                $scope.data[i][$scope.columns[b].name] = '';
                            }
                        }
                    }

                    loadRelation(function () {
                        if (typeof callback == "function") {
                            callback();
                        }
                    });

                }
                prepareData();

                // watch datasource changes
                $scope.$watch('datasource.data', function (n, o) {
                    if (n !== o && !$scope.edited && !$scope.loadingRelation) {
                        $scope.loaded = true;
                        var executeGroup = ($scope.dtGroups);
                        prepareData(function () {
                            if (executeGroup) {
                                $scope.dtGroups.group($scope.ht);
                                $scope.edited = true;
                            }

                            $("#" + $scope.renderID).handsontable('getInstance').loadData($scope.data);

                            $timeout(function () {
                                $scope.edited = false;
                            });
                        });
                    }
                }, true);

                // Generate DataTable Options -- start
                $timeout(function () {
                    evalArray($scope.gridOptions);

                    // initialize data table groups
                    if ($scope.gridOptions.groups) {
                        $scope.dtGroups = new Handsontable.DataTableGroups({
                            groupCols: $scope.$eval($scope.gridOptions.groups),
                            scope: $scope,
                            columns: columnsInternal,
                            totalGroups: $scope.gridOptions.totalGroups,
                            colHeaders: colHeaders,
                        }).prepare();

                        delete($scope.gridOptions.groups);

                        if ($scope.data.length > 0) {
                            $scope.dtGroups.group($scope.ht);
                        }
                    }

                    var options = $.extend({
                        data: $scope.data,
                        minSpareRows: $scope.gridOptions.readOnly ? 0 : (!$scope.dtGroups ? 1 : 0),
                        columnSorting: !$scope.dtGroups,
                        contextMenu: true,
                        scope: $scope,
                        colHeaders: colHeaders,
                        columns: columnsInternal,
                        autoWrapRow: true,
                        autoWrapCol: true,
                        mergeCells: true,
                        comments: true,
                        manualColumnResize: true,
                        cells: function (row, col, prop) {
                            var cellProperties = {};
                            if ($scope.dtGroups && $scope.data[row] && $scope.data[row]['__dt_flg']) {
                                switch ($scope.data[row]['__dt_flg']) {
                                    case 'E':
                                        cellProperties.className = 'empty';
                                        cellProperties.readOnly = true;

                                        break;
                                    case 'G':
                                        cellProperties.className = 'groups';
                                        cellProperties.readOnly = true;
                                        cellProperties.renderer = 'html';
                                        break;
                                    case 'T':
                                        var c = $scope.dtGroups.totalGroups[$scope.dtGroups.columns[col].name];
                                        if (c.trim().substr(0, 4) != 'span') {
                                            cellProperties.type = 'numeric';
                                            cellProperties.format = '0,0.00';
                                        }
                                        cellProperties.className = 'total';
                                        cellProperties.readOnly = true;
                                        break;
                                    default:
                                        cellProperties.className = '';
                                        break;
                                }
                            }
                            return cellProperties;
                        },
                        beforeAutofill: function (s, e, d) {
                            if (typeof $scope.events.beforeAutofill == "function") {
                                return $scope.events.beforeAutofill(s, e, d);
                            }

                            if (s.col == e.col && d.length > 1) {
                                var ht = $("#" + $scope.renderID).handsontable('getInstance');
                                var col = columnsInternal[s.col].data;
                                var seq = Math.abs(d[0][0] - d[1][0]);

                                if (!isNaN(seq) && seq > 0) {
                                    var se = (d[d.length - 1][0] * 1);
                                    $scope.edited = true;
                                    $timeout(function () {
                                        for (i = s.row; i <= e.row; i++) {
                                            $scope.data[i][col] = (se * 1) + seq;
                                            se = $scope.data[i][col];
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
                                $scope.beforeCellEdit(ch[3], ch[0], ch[1], $scope.data[ch[0]], ht);
                            }


                            if (typeof $scope.events.beforeChange == "function") {
                                $scope.events.beforeChange(changes, source);
                            }
                        },
                        beforeKeyDown: function (event) {
                            if (typeof $scope.events.beforeKeyDown == "function") {
                                $scope.events.beforeKeyDown(events);
                            }
                        },
                        afterSelectionEnd: function (r, c, r2, c2) {
                            if (typeof $scope.events.afterSelectionEnd == "function") {
                                $scope.events.afterSelectionEnd(events);
                            }

                            if (typeof $scope.gridOptions.afterSelectionChange == "function") {
                                $scope.gridOptions.afterSelectionChange($scope.data[r]);
                            }
                        },
                        afterChange: function (changes, source) {
                            var ht = $("#" + $scope.renderID).handsontable('getInstance');
                            if (typeof $scope.afterCellEdit == "function" && source == "edit") {
                                var ch = changes[0];
                                // afterCellEdit(value, row, col, data, ht);
                                $scope.afterCellEdit(ch[3], ch[0], ch[1], $scope.data[ch[0]], ht);
                            }

                            if (typeof $scope.events.afterChange == "function") {
                                $scope.events.afterChange(changes, source, $scope.grid());
                            }

                            //change datasource
                            switch (true) {
                                case source == "edit":
                                    var c = changes[0];
                                    break;
                                case ($scope.dtGroups && $scope.dtGroups.changed):
                                    break;
                                default:
                                    $scope.datasource.data = $scope.data;
                                    break;
                            }

                            $timeout(function () {
                                if ($scope.dtGroups && !$scope.dtGroups.changed) {
                                    $scope.dtGroups.calculate(changes, source, ht);
                                    ht.render();
                                }

                                $timeout(function () {
                                    $scope.edited = false;
                                });
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
                            if (typeof $scope.events.afterRender == "function") {
                                $scope.events.afterRender();
                            }
                        },
                        afterLoadData: function () {
                            $timeout(function () {
                                if (!$scope.gridLoaded && typeof $scope.onGridLoaded == "function") {
                                    $scope.onGridLoaded(options);
                                    $scope.gridLoaded = true;
                                }
                            });
                            if (typeof $scope.events.afterLoadData == "function") {
                                $scope.events.afterLoadData();
                            }
                        },
                        beforeColumnSort: function (column, order) {
                            if (typeof $scope.events.beforeColumnSort == "function") {
                                $scope.events.beforeColumnSort(column, column);
                            }
                        },
                        afterColumnSort: function (column, order) {
                            if (typeof $scope.events.afterColumnSort == "function") {
                                $scope.events.afterColumnSort(column, column);
                            }
                        },
                        beforeRender: function () {
                            $el.find('.header-grouping').remove();

                            var ht = $("#" + $scope.renderID).handsontable('getInstance');

                            for (i in columnsInternal) {
                                var c = columnsInternal[i];
                                if (c.options && c.options.width) {
                                    ht.setCellMeta(0, i, 'width', c.options.width);
                                }
                            }

                            if (typeof $scope.events.beforeRender == "function") {
                                $scope.events.beforeRender();
                            }

                            $timeout.cancel(renderTimeout);
                            renderTimeout = $timeout(function () {
                                $scope.loaded = true;
                            });
                        },
                        modifyColWidth: function () {
                            $el.find('.header-grouping').remove();

                            if (typeof $scope.events.modifyColWidth == "function") {
                                $scope.events.modifyColWidth();
                            }
                        },
                        contextMenu: $scope.contextMenu()
                    }, $scope.gridOptions);

                    //prepare data table groups   

                    // if there is beforeGridLoaded event, call it.
                    if (typeof $scope.beforeGridLoaded == "function") {
                        $scope.beforeGridLoaded(options);
                    }
                    // Generate DataTable Options -- end


                    // Setup Data Watcher                    
                    if (options.events) {
                        $scope.events = options.events;
                    }

                    if (!!$scope.events) {
                        options = $.extend(options, $scope.events);
                    }
                    $scope.columns = columnsInternal;

                    $("#" + $scope.renderID).handsontable(options);
                    $scope.ht = $("#" + $scope.renderID).handsontable('getInstance');

                });

                parent[$scope.name] = $scope;
            }
        }
    }
});