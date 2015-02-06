app.directive('psDataTable', function ($timeout, $http, $compile, $filter, $q) {
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
                                    if (!!array['target'] && array['target'] == '_blank') {
                                        window.open(eval($scope.generateUrl(url, 'function')), '_blank');
                                    } else {
                                        location.href = eval($scope.generateUrl(url, 'function'));
                                    }
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
                $scope.name = $el.find("data[name=name]").text();
                parent[$scope.name] = $scope;

                $scope.$q = $q;
                $scope.$http = $http;
                $scope.canAddRow = false;
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.datasource = parent[$el.find("data[name=datasource]").text()];
                $scope.data = [];
                $scope.relationColumns = [];
                $scope.dtGroups = null;
                $scope.getInstance = function () {
                    return $("#" + $scope.renderID).handsontable('getInstance');
                }
                $scope.loading = true;
                $scope.loaded = false;
                $scope.$watch('datasource.loading', function (n, o) {
                    if (n) {
                        $scope.loading = true;
                    } else {
                        $timeout(function () {
                            if ($scope.loaded) {
                                if ($scope.datasource.data == 0) {
                                    $scope.loading = false;
                                }
                            }
                        }, 100);
                    }
                });

                $scope.$container = $el.parents('.container-full');
                $scope.contextMenu = function () {
                    var menu;
                    if ($scope.dtGroups) {
                        menu = $scope.dtGroups.contextMenu();
                    } else {
                        menu = {
                            row_above: {},
                            row_below: {},
                            hsep1: '---------',
                            remove_row: {},
                            hsep2: '---------',
                            undo: {},
                            redo: {}
                        };
                    }
                    if (typeof $scope.gridOptions.removeMenu == "string") {
                        $scope.gridOptions.removeMenu = $scope.$eval($scope.gridOptions.removeMenu);
                    }
                    if (typeof $scope.gridOptions.removeMenu == "object" && $scope.gridOptions.removeMenu.length > 0) {
                        $scope.gridOptions.removeMenu.forEach(function (item) {
                            if (!!menu[item]) {
                                delete menu[item];
                            }
                        });
                    }

                    if ($scope.gridOptions.readOnly) {
                        delete menu['hsep1'];
                        delete menu['hsep2'];
                        delete menu['remove_row'];
                        if (!$scope.dtGroups) {
                            delete menu['row_above'];
                            delete menu['row_below'];
                        } else {
                            delete menu['insert'];
                            delete menu['duplicate'];
                        }
                    }

                    return menu;
                }

                $scope.reset = function () {
                    $scope.resetPageSetting();
                    location.reload();
                }

                $scope.updateCell = function () {
                };

                $scope.$timeout = $timeout;
                // setup internal variables
                var colHeaders = [];
                var colWidths = [];
                var columnsInternal = [];
                var loadTimeout = null;
                var renderTimeout = null;
                var categories = [];
                var lastCat = '';

                // add columns from datasource when columns definition is empty
                $scope.colGenerated = false;
                $scope.generateCols = function () {
                    if ($scope.colGenerated)
                        return;
                    $scope.colGenerated = true;

                    for (i in $scope.dataSource1.data[0]) {
                        if (i == 'id')
                            continue;
                        $scope.columns.push({
                            name: i,
                            label: i,
                            options: {}
                        });
                    }
                }

                // assemble each columns -- start
                $scope.colAssembled = false;
                $scope.relSuffix = "_label_datatable";
                $scope.assembleCols = function () {
                    if ($scope.colAssembled || $scope.columns.length == 0)
                        return;
                    $scope.colAssembled = true;

                    categories = [];
                    columnsInternal = [];
                    colHeaders = [];
                    colWidths = [];

                    if (typeof $scope.initColumns == "function") {
                        $scope.columns = $scope.initColumns($scope.columns);
                    }

                    for (var i in $scope.columns) {
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
                                colDef.data = c.name + $scope.relSuffix;
                                colDef.type = "autocomplete";
                                colDef.renderer = "relation";
                                colDef.relList = {};
                                colDef.editor = "relation";
                                colDef.scope = $scope;
                                colDef.validator = Handsontable.editors.RelationEditor.prototype.checkRel;
                                colDef.source = Handsontable.editors.RelationEditor.prototype.search;
                                break;
                            default:
                                colDef.renderer = "text";
                                if (typeof c.stringAlias == "object" && !$.isArray(c.stringAlias)) {
                                    colDef.renderer = "stringalias";
                                }
                                switch (c.inputMask) {
                                    case "number":
                                        colDef.type = 'numeric';

                                        var dec = '00';
                                        if (typeof c.options.decimal != "undefined") {
                                            dec = "";
                                            for (var de = 0; de < c.options.decimal * 1; de++) {
                                                dec += "0";
                                            }
                                        }

                                        colDef.format = '0,0.' + dec;
                                        delete(colDef.renderer);
                                        break;
                                    case "date":
                                        c.inputMask = "99/99/9999";
                                        colDef.renderer = 'datetime';
                                        colDef.editor = 'mask';
                                        colDef.filter = $filter;
                                        break;
                                    case "datetime":
                                        c.inputMask = "99/99/9999 99:99";
                                        colDef.renderer = 'datetime';
                                        colDef.editor = 'mask';
                                        colDef.filter = $filter;
                                        break;
                                    case "time":
                                        c.inputMask = "99:99";
                                        colDef.renderer = 'datetime';
                                        colDef.editor = 'mask';
                                        colDef.filter = $filter;
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

                        // add columns
                        columnsInternal.push(col);
                        colHeaders.push(c.label);
                        colWidths.push(c.options.width || 70)

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

                }
                // assemble each columns -- end
                $scope.fowTimer = null;
                $scope.fixOtherWidth = function () {
                    if ($scope.fowTimer) {
                        $timeout.cancel($scope.fowTimer);
                    }
                    $scope.fowTimer = $timeout(function () {
                        var w = $el.find(".htCore:eq(0)").width() + 30;
                        if (w > $('#content').width()) {
                            $el.parent().find("> .data-filter").width(w);
                            $el.parent().find("> .section-header").width(w - 40);
                            $(".form-horizontal > .alert").width(w - 60);
                        }
                    }, 100);
                }

                $scope.fixHeight = function () {
                    var dt = $el.find(".dataTable");
                    dt.css('overflow', 'visible');
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
                    if (!!fh.form.position()) {
                        fh.formTopPos = Math.abs(fh.form.position().top - fh.form.offset().top);
                        fh.formTop = fh.form.offset().top;
                    }
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
                $scope.countDgr = function () {
                    var dgr = {};
                    var relCols = [];
                    for (i in columnsInternal) {
                        if (columnsInternal[i].columnType == "relation") {
                            relCols.push(columnsInternal[i]);
                        }
                    }
                    for (i in $scope.columns) {
                        if ($scope.columns[i].columnType == "relation") {
                            relCols.push($scope.columns[i]);
                        }
                    }
                    if ($scope.dtGroups) {
                        for (i in $scope.dtGroups.groupColOpts) {
                            if ($scope.dtGroups.groupColOpts[i].columnType == "relation") {
                                relCols.push($scope.dtGroups.groupColOpts[i]);
                            }
                        }
                    }

                    for (var i in $scope.datasource.data) {
                        var d = $scope.datasource.data[i];
                        for (var ir in relCols) {
                            var r = relCols[ir];
                            var id = d[r.name];
                            var name = $scope.name;
                            var cls = $scope.modelClass;
                            var col = r.name;
                            dgr['name'] = name;
                            dgr['class'] = cls;
                            dgr['cols'] = dgr['cols'] || {};
                            dgr['cols'][col] = dgr['cols'][col] || [];
                            if (id != "" && id != null && dgr['cols'][col].indexOf(id) < 0) {
                                dgr['cols'][col].push(id);
                            }
                            if (!!$scope.model && !!$scope.model.id) {
                                dgr['model_id'] = $scope.model.id;
                            }

                        }
                    }

                    return {
                        dgr: dgr,
                        rel: relCols
                    };
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


                $scope.isRelation = function (prop) {
                    return $scope.relationColumns.indexOf(prop.replace($scope.relSuffix, '') + $scope.relSuffix) >= 0;
                }
                // Load Relation -- start
                $scope.loadRelation = function (callback, countDgr) {
                    if ($scope.data.length == 0) {
                        callback();
                        return;
                    }

                    $scope.triggerRelationWatch = false;
                    if (typeof countDgr == "undefined") {
                        countDgr = $scope.countDgr();
                    }
                    var relCols = countDgr.rel;
                    var dgr = countDgr.dgr;

                    relCols.forEach(function (i) {
                        if ($scope.relationColumns.indexOf(i.name) < 0) {
                            $scope.relationColumns.push(i.name);
                        }
                    });


                    if (relCols.length > 0 && dgr.name) {
                        $scope.loadingRelation = true;
                        var url = Yii.app.createUrl('/formfield/RelationField.dgrInit');
                        if ($scope.httpRelReq) {
                            $scope.httpRelReq.resolve();
                        }
                        $scope.httpRelReq = $q.defer();
                        $http.post(url, dgr, {
                            timeout: $scope.httpRelReq.promise
                        }).success(function (data) {
                            for (var rowIdx in $scope.data) {
                                var row = $scope.data[rowIdx];
                                for (var dataIdx in data) {
                                    var d = data[dataIdx];

                                    if ($scope.dtGroups && $scope.dtGroups.groupCols.indexOf(dataIdx) >= 0) {
                                        var col = $scope.dtGroups.groupCols[row['__dt_lvl']];
                                        if (row['__dt_flg'] == "G" && dataIdx == col) {
                                            for (var i in d) {
                                                if (d[i].value == row[$scope.columns[0].name]) {
                                                    row[$scope.columns[0].name] = d[i].label;
                                                    break;
                                                }
                                            }
                                            continue;
                                        }
                                    }

                                    if (row[dataIdx]) {
                                        for (var i in d) {
                                            if (d[i].value == row[dataIdx]) {
                                                row[dataIdx + $scope.relSuffix] = d[i].label;
                                                break;
                                            }
                                        }
                                    }
                                    if (!row[dataIdx + $scope.relSuffix]) {
                                        row[dataIdx + $scope.relSuffix] = '';
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
                    function isNumber(n) {
                        return typeof n == 'number' && !isNaN(n - n);
                    }

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

                    $scope.loadRelation(function () {
                        if (typeof callback == "function") {
                            callback();
                        }
                    });
                }
                if ($scope.datasource.data > 0) {
                    prepareData();
                }

                // Watch datasource changes
                $scope.dsChangeTimer = null;
                $scope.dsChange = function () {
                    function doChange() {
                        var executeGroup = ($scope.dtGroups);
                        if (executeGroup && $scope.dtGroups.grouped) {
                            $scope.dtGroups.ungroup($scope.ht, false);
                        }

                        prepareData(function () {
                            $scope.ht = $scope.getInstance();
                            if (executeGroup) {
                                $scope.dtGroups.group($scope.ht);
                                $scope.edited = true;
                            }
                            if ($scope.ht) {
                                $scope.ht.loadData($scope.data);
                                $timeout(function () {
                                    $scope.edited = false;
                                });
                                $scope.ht.render();
                            }
                            $scope.loading = false;
                        });
                    }

                    if ($scope.datasource.data) {
                        if (!!$scope.datasource.data.length && $scope.datasource.data.length > 0 &&
                                Object.keys($scope.datasource.data[0]).length > 0 && $scope.notReady) {
                            prepareData(function () {
                                $scope.init();
                                $scope.notReady = false;
                                $timeout(function () {
                                    doChange();
                                });
                            });
                        } else {
                            doChange();
                        }
                    } else {
                        $scope.loading = false;
                    }
                }

                // Hook up Data Source Watcher.
                $scope.datasource.afterQueryInternal[$scope.renderID] = $scope.dsChange;
                $scope.$watch('datasource.data', function (n, o) {
                    if (n !== o && (!$scope.edited || $scope.data.length == 0) && !$scope.loadingRelation) {
                        if (n > 0 && $scope.edited == false) {
                            $scope.dsChange();
                        }
                    }
                }, true);

                // Prepare to initialize data-table
                $scope.notReady = true;
                $timeout(function () {
                    if ($scope.datasource.data.length > 0 || $scope.columns.length > 0) {
                        $scope.notReady = false;
                        $scope.canAddRow = true;

                        if ($scope.gridOptions.removeMenu) {
                            if (typeof $scope.gridOptions.removeMenu == "string") {
                                $scope.gridOptions.removeMenu = $scope.$eval($scope.gridOptions.removeMenu);
                            }

                            if ($scope.gridOptions.removeMenu.indexOf("row_above") || $scope.gridOptions.removeMenu.indexOf("row_below") || $scope.gridOptions.removeMenu.indexOf("insert") || $scope.gridOptions.removeMenu.indexOf("duplicate")) {
                                $scope.canAddRow = false;
                            }
                        }

                        prepareData(function () {
                            $scope.init();
                        });
                    } else {
                        $scope.loaded = true;
                        $scope.loading = false;
                        $scope.canAddRow = false;
                    }
                });

                $scope.addRow = function () {
                    if (!$scope.dtGroups) {
                        var newRow = {};
                        $scope.data.push(newRow);
                        $scope.datasource.data.push(newRow);
                    } else {
                        $scope.dtGroups.addRow();
                    }
                }

                // Initialize data-table
                $scope.init = function () {
                    if ($scope.columns.length == 0 && $scope.datasource.data.length > 0) {
                        $scope.generateCols();
                    }
                    $scope.assembleCols();

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

                        // check for link mode
                        var multiSelect = true;
                        if (typeof $scope.gridOptions.afterSelectionChange == "function") {
                            minSpareRows = 0;
                            $scope.gridOptions.readOnly = true;
                            $el.addClass('link-mode');
                            multiSelect = false;
                        }
                        if (!!$scope.gridOptions.readOnly) {
                            $el.addClass('read-only');
                        }

                        // set current row class name
                        var currentRowClassName = columnsInternal.length > 3 ? 'currentCol' : '';
                        if (typeof $scope.gridOptions.afterSelectionChange == "function") {
                            currentRowClassName = '';
                        }

                        if (typeof $scope.gridOptions.colWidths == "string") {
                            colWidths = $scope.$eval($scope.gridOptions.colWidths);
                        }


                        var options = $.extend({
                            data: $scope.data,
                            columnSorting: !$scope.dtGroups,
                            contextMenu: true,
                            multiSelect: multiSelect,
                            fillHandle: !$scope.gridOptions.readOnly,
                            scope: $scope,
                            colHeaders: colHeaders,
                            columns: columnsInternal,
                            colWidths: colWidths,
                            autoWrapRow: true,
                            autoWrapCol: true,
                            mergeCells: true,
                            comments: true,
                            currentRowClassName: 'currentRow',
                            currentColClassName: currentRowClassName,
                            manualColumnResize: true,
                            cells: function (row, col, prop) {
                                var cellProperties = {};
                                cellProperties.$scope = $scope;

                                if ($scope.dtGroups) {
                                    function setDefault() {
                                        cellProperties.className = 'group-text';
                                        cellProperties.readOnly = false;
                                        if (!!$scope.columns[col] && typeof $scope.columns[col].options.enableCellEdit == "boolean") {
                                            cellProperties.readOnly = !$scope.columns[col].options.enableCellEdit;
                                        }

                                        if (!!$scope.gridOptions.readOnly) {
                                            cellProperties.readOnly = true;
                                        }

                                        if (col == 0) {
                                            cellProperties.renderer = 'groups';
                                        }
                                    }

                                    if ($scope.data[row] && $scope.data[row]['__dt_flg']) {
                                        switch ($scope.data[row]['__dt_flg']) {
                                            case 'E':
                                                cellProperties.className = 'empty';
                                                cellProperties.readOnly = true;
                                                cellProperties.type = "text";
                                                break;
                                            case 'G':
                                                cellProperties.className = 'groups';
                                                cellProperties.type = "text";

                                                if (col > 0) {
                                                    cellProperties.readOnly = true;
                                                } else {
                                                    var row = $scope.data[row];
                                                    var colProp = $scope.dtGroups.groupCols[row['__dt_lvl']];

                                                    var colDef = $scope.dtGroups.groupColOpts[colProp];
                                                    if (typeof colDef == "undefined") {
                                                        colDef = $scope.dtGroups.groupColOpts[colProp + $scope.relSuffix];
                                                    }
                                                    if (colDef) {
                                                        $.extend(cellProperties, colDef);
                                                    } else {
                                                        cellProperties.readOnly = true;
                                                    }
                                                }

                                                cellProperties.renderer = 'groups';

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
                                                setDefault();
                                                break;
                                        }
                                    } else {
                                        setDefault();
                                    }
                                }

                                if (typeof $scope.updateCell == "function") {
                                    $scope.updateCell(row, col, prop, cellProperties);
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
                                    $scope.beforeCellEdit(ch[3], ch[0], ch[1], $scope.data[ch[0]], ht);
                                }

                                switch (source) {
                                    case "edit":
                                    case "paste":
                                    case "autofill":
                                        $timeout(function () {
                                            changes.map(function (c) {
                                                if (typeof $scope.beforeCellEdit == "function") {
                                                    if ($scope.dtGroups) {
                                                        var row = $scope.datasource.data[$scope.data[c[0]]['__dt_row']] = $scope.data[c[0]];
                                                        row[c[1]] = c[3];
                                                        $scope.beforeCellEdit(c[3], c[0], c[1], row);
                                                    } else {
                                                        var row = $scope.datasource.data[c[0]] = $scope.data[c[0]];
                                                        $scope.beforeCellEdit(c[3], c[0], c[1], row);
                                                    }
                                                }
                                            });

                                            if (typeof $scope.beforeCellEdit == "function") {
                                                if (!$scope.ht || typeof $scope.ht.render != "function") {
                                                    $scope.ht = $scope.getInstance();
                                                }

                                                $scope.ht.render();
                                            }
                                        });
                                        break;
                                }

                                if (typeof $scope.events.beforeChange == "function") {
                                    $scope.events.beforeChange(changes, source);
                                }
                            },
                            beforeOnCellMouseDown: function (event, coords, TD) {
                                if (typeof $scope.events.beforeOnCellMouseDown == "function") {
                                    $scope.events.beforeOnCellMouseDown(event, coords, TD);
                                }

                                if (typeof $scope.gridOptions.afterSelectionChange == "function" && $(TD).is('td')) {
                                    if (!$scope.dtGroups || (!!$scope.dtGroups && $scope.data[coords.row]['__dt_flg'] == "Z")) {
                                        $scope.gridOptions.afterSelectionChange($scope.data[coords.row]);
                                    }
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
                                if (!$scope.mouseDown) {
                                    $scope.fixScroll();
                                }
                                $scope.mouseDown = false;

                            },
                            afterRemoveRow: function (index, amount) {
                                if (!$scope.dtGroups) {
                                    $scope.edited = true;
                                    $scope.datasource.data.splice(index, amount);
                                }
                            },
                            afterValidate: function (valid, value, row, prop, source) {
                                if (typeof $scope.events.afterValidate == "function") {
                                    $scope.events.afterValidate(valid, value, row, prop, source);
                                }
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
                                            case "autofill":
                                                $timeout(function () {
                                                    changes.map(function (c) {
                                                        if ($scope.dtGroups) {
                                                            $scope.dtGroups.handleChange($scope, c);
                                                        } else {
                                                            if (!$scope.datasource.data[c[0]]) {
                                                                $scope.datasource.data[c[0]] = {};
                                                            }
                                                            $scope.datasource.data[c[0]][c[1]] = c[3];
                                                        }

                                                        if (typeof $scope.afterCellEdit == "function") {
                                                            if ($scope.dtGroups) {
                                                                var row = $scope.datasource.data[$scope.data[[0]]['__dt_row']] = $scope.data[[0]];
                                                                $scope.afterCellEdit(c[3], c[0], c[1], row);
                                                            } else {
                                                                var row = $scope.datasource.data[c[0]] = $scope.data[c[0]];
                                                                $scope.afterCellEdit(c[3], c[0], c[1], row);
                                                            }
                                                        }
                                                    });

                                                    if (typeof $scope.afterCellEdit == "function" && $scope.ht) {
                                                        if (!$scope.ht || typeof $scope.ht.render != "function") {
                                                            $scope.ht = $scope.getInstance();
                                                        }

                                                        $scope.ht.render();
                                                    }
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

                                if (typeof $scope.events.afterChange == "function") {
                                    $scope.events.afterChange(changes, source, $scope.grid());
                                }


                                $timeout(function () {
                                    $scope.edited = false;
                                });
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

                                if (typeof $scope.events.beforeRender == "function") {
                                    $scope.events.beforeRender();
                                }

                                $timeout.cancel(renderTimeout);
                                renderTimeout = $timeout(function () {
                                    $scope.loaded = true;
                                });
                            },
                            modifyColWidth: function () {
                                $scope.fixOtherWidth();
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
                            $scope.ht = $("#" + $scope.renderID).handsontable(options);

                            $timeout(function () {
                                $scope.loaded = true;
                                $scope.loading = false;
                            });
                        }
                    });
                }
            }
        }
    }
});
