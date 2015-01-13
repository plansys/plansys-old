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
                $scope.Math = window.Math;
                $scope.grid = function (command) {
                    command = command || 'getInstance';
                    return $("#" + $scope.renderID).handsontable(command);
                };
                $scope.edited = false;
                $scope.loadingRelation = false;
                $scope.triggerRelationWatch = true;
                $scope.relAvailable = false;
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
                $scope.$container = $el.parents('.container-full');
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
                // add columns from datasource when columns definition is empty
                if ($scope.columns.length == 0) {
                    if ($scope.dataSource1.data && $scope.dataSource1.data.length > 0) {
                        for (i in $scope.dataSource1.data[0]) {
                            if (i == 'id')
                                continue;
                            $scope.columns.push({
                                name: i,
                                label: i
                            });
                        }
                    }
                }

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
                            $scope.relAvailable = true;
                            colDef.type = "dropdown";
                            if (c.listType == 'js') {
                                c.listItem = parent.$eval(col.listExpr);
                            }
                            colDef.source = parent.$eval(c.listItem);
                            break;
                        case "relation":
                            $scope.relAvailable = true;
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
                                                    $scope.lastRelList[data[i].label.trim('"')] = data[i].value;
                                                }
                                                if (labels.length && labels.length > 0) {
                                                    process(labels);
                                                }
                                            });
                                }
                            };
                            break;
                        case "string":
                            colDef.renderer = "text";
                            if (typeof c.stringAlias == "object" && !$.isArray(c.stringAlias)) {
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


                    if (c.options && !!c.options.enableCellEdit) {
                        if (c.options.enableCellEdit.trim().substr(0, 3) === "js:") {
                            c.options.enableCellEdit = $scope.$parent.$eval(c.options.enableCellEdit.trim().substr(3));
                        } else if (typeof c.options.enableCellEdit == "string") {
                            c.options.enableCellEdit = $scope.$parent.$eval(c.options.enableCellEdit.trim());
                        }

                        if (!c.options.enableCellEdit) {
                            colDef.readOnly = true;
                        }
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

                $scope.fhTimer = null;
                $scope.fixHeight = function () {
                    if ($scope.fhTimer != null) {
                        clearTimeout($scope.fhTimer);
                    }

                    $scope.fhTimer = setTimeout(function () {
                        $el
                                .find(".dataTable")
                                .height($el.find(".htContainer .htCore:eq(0)").height() + 22)
                                .css('overflow', 'visible');
                    }, 1000);
                }

                $scope.fixScroll = function () {
                    var c = $el.find('td.current');
                    if (c.length == 0)
                        return;
                    var w = c[0].offsetWidth;
                    var h = c[0].offsetHeight;
                    var o = c.offset();
                    var p = c.position();

                    // scroll horizontal
                    var sl = $scope.$container.scrollLeft();
                    var slw = o.left + w + 17;
                    if (o.left < 0) {
                        $scope.$container.scrollLeft(sl + o.left);
                    } else if (slw > $scope.$container.width()) {
                        $scope.$container.scrollLeft(sl + (slw - $scope.$container.width()));
                    }

                    // scroll vertical
                    var st = $scope.$container.scrollTop();
                    var sth = o.top + h - 90;
                    if (o.top < 105) {
                        $scope.$container.scrollTop(st - (110 - o.top));
                    } else if (sth > $scope.$container.height()) {
                        $scope.$container.scrollTop(st + (sth - $scope.$container.height()));
                    }
                }

                $scope.fixClone = function () {
                    var sl = $scope.$container.scrollLeft();
                    var st = $scope.$container.scrollTop();
                    var rh = $el.find('.rowHeader:eq(0)');
                    var cl = $el.find('.ht_clone_left');
                }
                $scope.fixComments = function () {
                    $("body > .htComments").css('margin-top', $scope.$container.scrollTop() * -1);
                }

                $scope.$container.on('scroll', function () {
                    $scope.fixClone();
                    $scope.fixComments();
                });
                // fixHead
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

                // pagingOptions
                if ($scope.gridOptions['enablePaging']) {
                    $scope.gridOptions.pagingOptions = {
                        pageSizes: [25, 50, 100, 250, 500],
                        pageSize: 25,
                        totalServerItems: $scope.datasource.totalItems,
                        currentPage: 1
                    };
                    var timeout = null;
                    $scope.$watch('gridOptions.pagingOptions', function (paging, oldpaging) {
                        if (paging != oldpaging) {
                            var ds = $scope.datasource;
                            var maxPage = Math.ceil($scope.datasource.totalItems / $scope.gridOptions.pagingOptions.pageSize);
                            if (isNaN($scope.gridOptions.pagingOptions.currentPage) || $scope.gridOptions.pagingOptions.currentPage == '') {
                                $scope.gridOptions.pagingOptions.currentPage = 1;
                            }

                            if ($scope.gridOptions.pagingOptions.currentPage > maxPage) {
                                $scope.gridOptions.pagingOptions.currentPage = maxPage;
                            }

                            if (typeof ds != "undefined") {
                                if (timeout != null) {
                                    clearTimeout(timeout);
                                }
                                timeout = setTimeout(function () {
                                    ds.updateParam('currentPage', paging.currentPage, 'paging');
                                    ds.updateParam('pageSize', paging.pageSize, 'paging');
                                    ds.updateParam('totalServerItems', paging.totalServerItems, 'paging');
                                    ds.query();
                                }, 100);
                            }
                        }
                    }, true);
                    $scope.pageForward = function () {
                        $scope.gridOptions.pagingOptions.currentPage++;
                    }

                    $scope.pageBackward = function () {
                        $scope.gridOptions.pagingOptions.currentPage--;
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
                function isNumber(n) {
                    return typeof n == 'number' && !isNaN(n - n);
                }
                function prepareData(callback) {
                    $scope.data = angular.copy($scope.datasource.data);
                    for (i in $scope.data) {
                        for (b in $scope.columns) {
                            if ($scope.columns[b].name &&
                                    !isNumber($scope.data[i][$scope.columns[b].name]) &&
                                    !$scope.data[i][$scope.columns[b].name]
                                    ) {
                                $scope.data[i][$scope.columns[b].name] = '';
                            }
                        }

                        var item = $scope.data[i];
                        for (k in item) {
                            if (item[k] == null) {
                                item[k] = '';
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
                    if (n !== o && (!$scope.edited || $scope.data.length == 0) && !$scope.loadingRelation) {
                        $scope.loaded = true;
                        var executeGroup = ($scope.dtGroups);
                        if (executeGroup && $scope.dtGroups.grouped) {
                            $scope.dtGroups.ungroup($scope.ht);
                        }

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
                            if (!$scope.getInstance()) {
                                $timeout(function () {
                                    $scope.ht = $scope.getInstance();
                                    $scope.dtGroups.group($scope.ht);
                                });
                            }
                        } else {
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
                        currentRowClassName: 'currentRow',
                        currentColClassName: 'currentCol',
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
                                        } else {
                                            cellProperties.renderer = 'html';
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
                                var seq = d[1][0] - d[0][0];
                                if (!isNaN(seq)) {
                                    var se = (d[d.length - 1][0] * 1);
                                    var length = Math.abs(s.row - e.row) + 1;
                                    var last = d[d.length - 1];
                                    d.length = 0;
                                    for (var i = 0; i < length; i++) {
                                        last = last * 1 + seq * 1;
                                        d.push([last + ""]);
                                    }

                                    return d;
                                }
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
                            $scope.fixHeight();
                        },
                        afterOnCellMouseDown: function (event, coords, TD) {
                            if (typeof $scope.events.afterOnCellMouseDown == "function") {
                                $scope.events.afterOnCellMouseDown(event, coords, TD);
                            }

                            $scope.mouseDown = true;
                        },
                        afterSelection: function (r, c, r2, c2) {
                            if (typeof $scope.events.afterSelection == "function") {
                                $scope.events.afterSelection(r, c, r2, c2);
                            }

                            if (!$scope.mouseDown) {
                                $scope.fixScroll();
                            }
                        },
                        afterSelectionEnd: function (r, c, r2, c2) {
                            if (typeof $scope.events.afterSelectionEnd == "function") {
                                $scope.events.afterSelectionEnd(r, c, r2, c2);
                            }

                            if (typeof $scope.gridOptions.afterSelectionChange == "function") {
                                $scope.gridOptions.afterSelectionChange($scope.data[r]);
                            }
                            if (!$scope.mouseDown) {
                                $scope.fixScroll();
                            }
                            $scope.mouseDown = false;
                        },
                        afterRemoveRow: function (index, amount) {
                            $scope.datasource.data.splice(index, amount);
                        },
                        afterChange: function (changes, source) {
                            //watch datasource changes
                            switch (true) {
                                case ($scope.dtGroups && $scope.dtGroups.changed):
                                    break;
                                default:
                                    switch (source) {
                                        case "edit":
                                        case "paste":
                                            $timeout(function () {
                                                changes.map(function (c) {
                                                    if ($scope.dtGroups) {
                                                        var row = $scope.data[c[0]]['__dt_row'];
                                                        $scope.datasource.data[row][c[1]] = c[3];
                                                    } else {
                                                        if (!$scope.datasource.data[c[0]]) {
                                                            $scope.datasource.data[c[0]] = {};
                                                        }
                                                        $scope.datasource.data[c[0]][c[1]] = c[3];
                                                    }
                                                });
                                            });
                                            break;
                                        case "loadData":
                                            if (!$scope.edited) {
                                                $scope.datasource.data = angular.copy($scope.data);
                                            }
                                            break;
                                    }
                                    break;
                            }

                            var ht = $("#" + $scope.renderID).handsontable('getInstance');
                            if (typeof $scope.afterCellEdit == "function" && source == "edit") {
                                var ch = changes[0];
                                // afterCellEdit(value, row, col, data, ht);
                                $scope.afterCellEdit(ch[3], ch[0], ch[1], $scope.data[ch[0]], ht);
                            }

                            if (typeof $scope.events.afterChange == "function") {
                                $scope.events.afterChange(changes, source, $scope.grid());
                            }

                            $timeout(function () {
                                if (false && $scope.dtGroups && !$scope.dtGroups.changed && source != 'loadData') {
                                    $scope.dtGroups.calculate(changes, source, ht);
                                    ht.render();
                                }

                                $timeout(function () {
                                    $scope.edited = false;
                                });
                            });
                            $scope.fixHeight();
                        },
                        afterRender: function () {
                            if (categories.length > 0) {
                                //add category header
                                var html = '<tr class="header-grouping">';
                                if (!!options.rowHeaders) {
                                    html += '<th><div class="relative"><span class="rowHeader">&nbsp;</span></div></th>';
                                }

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
                            $scope.fixHeight();
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

                            //FIX HEIGHT OVERFLOW
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
                    if (!!$("#" + $scope.renderID)[0]) {
                        $("#" + $scope.renderID).width($el.width());
                        $("#" + $scope.renderID).handsontable(options);
                        $scope.ht = $("#" + $scope.renderID).handsontable('getInstance');
                    }
                });
                parent[$scope.name] = $scope;
            }
        }
    }
});