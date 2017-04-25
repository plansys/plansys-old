app.directive('psDataGrid', function ($timeout, $http, Upload, $compile, $ocLazyLoad, dateFilter) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                $upload= Upload;

                function evalArray(array, opt) {
                    opt = $.extend({
                        parseUrl: true
                    }, opt);

                    for (i in array) {
                        if (typeof array[i] == "string") {
                            if (array[i].trim().substr(0, 3) == "js:") {
                                eval('array[i] = ' + array[i].trim().substr(3));
                            } else if (opt.parseUrl && array[i].trim().substr(0, 4) == "url:") {
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

                $('body').on({
                    mouseover: function () {
                        var $container = $(this);
                        $('.ngRow > .ngCellButtonCollapsedDetail').each(function () {
                            var $container = $(this).parent().find('.' + $(this).attr('colt')).find('.ngCellButtonCollapsed');
                            $(this).hide().remove().appendTo($container);
                        });

                        var $detail = $(this).find('.ngCellButtonCollapsedDetail').remove();
                        var offset = {
                            right: $(this).parents('.ngCanvas').width() -
                                    ($(this).parents('.ngCell').css('left').replace('px', '') * 1 +
                                            $(this).parents('.ngCell').width())
                        };
                        $detail.attr('colt', $(this).parents('.ngCell').attr('class').split(' ').pop())
                                .css(offset)
                                .show();

                        $detail.appendTo($(this).parents('.ngRow'));
                        $compile($detail)(angular.element($container).scope());

                        $detail.on({
                            mouseout: function () {
                                $(this).hide().remove().appendTo($container);
                            }
                        });
                    }
                }, '.ngCellButtonCollapsed');

                $scope.pagingKeypress = function (e) {
                    if (typeof e != "undefined" && e.which == 13) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                };

                $scope.excelModeSelectedRow = null;
                $scope.excelModeSelChange = function (row, event) {
                    $scope.excelModeSelectedRow = row;
                };

                $scope.removeRow = function (row) {
                    if (typeof row == "undefined" || typeof row.rowIndex != 'number') {
                        return;
                    }

                    var index = row.rowIndex;
                    var oldData = $scope.data.splice(index, 1);
                    $timeout(function () {
                        if ($scope.data.length <= index) {
                            $scope.grid.selectedItems.length = 0;
                        } else {
                            $scope.grid.gridOptions.selectItem(index, true);
                        }

                        if (typeof $scope.afterRemove == "function") {
                            $scope.afterRemove(index, oldData);
                        }
                    }, 0);
                };

                $scope.isNotEmpty = function (data, except) {
                    var except = except || [];
                    var valid = false;
                    for (i in data) {
                        if (except.indexOf(i) >= 0) {
                            continue;
                        }

                        if (data[i] != "") {
                            valid = true;
                        }
                    }
                    return valid;
                }

                $scope.isNotEmpty = function (data, except) {
                    var except = except || [];
                    var valid = false;
                    for (i in data) {
                        if (except.indexOf(i) >= 0) {
                            continue;
                        }

                        if (data[i] != "") {
                            valid = true;
                        }
                    }
                    return valid;
                }

                $scope.addRow = function (row) {
                    if (typeof $scope.data == "undefined") {
                        $scope.data = [];
                    }

                    var data = {};
                    for (i in $scope.columns) {
                        data[$scope.columns[i].name] = '';
                    }

                    if (typeof row != "undefined" && row != null && typeof row.rowIndex == 'number') {
                        $scope.data.splice(row.rowIndex + 1, 0, data);
                    } else {
                        $scope.data.push(data);
                    }
                }

                $scope.buttonClick = function (row, e) {
                    $btn = $(e.target);

                    if (!$btn.is('a')) {
                        $btn = $btn.parents('a');
                    }

                    if ($btn.attr('confirm')) {
                        if (!confirm($btn.attr('confirm'))) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                    }

                    if ($btn.attr('ajax') == 'true') {
                        if ($btn.attr('ajax-success')) {

                            $http.get($btn.attr('href'))
                                    .success(function (data) {
                                        $scope.$eval($btn.attr('ajax-success'), {row: row, data: data});
                                    })
                                    .error(function (data) {
                                        $scope.$eval($btn.attr('ajax-failed'), {row: row, data: data});
                                    });
                        }

                        e.preventDefault();
                        e.stopPropagation();

                        return false;
                    }
                }

                $scope.generateUrlLink = function (content, opt, row) {
                    var url = opt.href;
                    if (opt.href.indexOf('url:') === 0) {
                        url = $scope.$eval($scope.generateUrl(opt.href.substr(4)), {row: row});
                    }

                    var target = !!opt.target ? 'target="' + opt.target + '"' : '';
                    return '<a href="' + url + '" ' + target + '>' + content + '</a>';
                }

                $scope.generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {

                        var match = url.match(/{([^}]+)}/g);
                        for (i in match) {
                            var m = match[i];
                            m = m.substr(1, m.length - 2);
                            var result = "' + row.getProperty('" + m + "') + '";
                            if (m.indexOf('.') > 0) {
                                result = $scope.$eval(m);
                            }
                            url = url.replace('{' + m + '}', result);
                        }

                        if (url.match(/http*/ig)) {
                            output = url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'");
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

                $scope.getEditableClass = function (col) {
                    if (!$scope.gridOptions['enableCellEdit'] && !$scope.gridOptions['enableExcelMode']) {
                        return '';
                    }

                    var editable = false;
                    if (typeof col != "undefined") {

                        if (!!col.options && !!col.options.enableCellEdit) {
                            editable = col.options.enableCellEdit !== false;
                        } else {
                            editable = true;
                        }
                    }

                    return editable ? '' : 'non-editable';
                }

                $scope.generateCell = function (col) {
                    var editableClass = $scope.getEditableClass(col);
                    var html = "<div class=\"ngCellText " + editableClass + "\" ng-class=\"col.colIndex()\">\n\
                                    <span ng-cell-text>{{COL_FIELD}}</span></div>";
                    return html;
                }

                $scope.stringAlias = function (value, field) {
                    if (!value)
                        return "";

                    var wildCard = false;
                    for (i in $scope.columns) {
                        if ($scope.columns[i].name == field) {
                            var newval = '';
                            for (k in $scope.columns[i].stringAlias) {

                                if (k.toLowerCase() == value.toLowerCase() || k.toLowerCase() == "'" + value.toLowerCase() + "'") {
                                    return $scope.columns[i].stringAlias[k];
                                }
                                if (k.indexOf('rx:') == 0) {
                                    eval("var regex = " + k.substr(3));
                                    var match = value.match(regex);
                                    if (match != null && match.length > 0) {
                                        return $scope.columns[i].stringAlias[k];
                                    }
                                }
                                if (k == '*') {
                                    wildCard = $scope.columns[i].stringAlias[k];
                                }
                            }
                        }
                    }

                    if (wildCard)
                        return wildCard;
                    return value;
                }

                $scope.prevPage = function () {
                    $scope.grid.pageBackward();
                    $scope.savePageSetting();
                }

                $scope.nextPage = function () {
                    $scope.grid.pageForward();
                    $scope.savePageSetting();
                }

                $scope.savePageSetting = function () {
                    $scope.pageSetting.dataGrids = $scope.pageSetting.dataGrids || {};
                    $scope.pageSetting.dataGrids[$scope.name] = {
                        sort: $scope.gridOptions.sortInfo,
                        paging: $scope.grid.pagingOptions
                    };
                }

                $scope.loadPageSetting = function () {
                    if (!!$scope.pageSetting.dataGrids && !!$scope.pageSetting.dataGrids[$scope.name]) {

                        var changing = false;
                        if (JSON.stringify($scope.gridOptions.sortInfo) != JSON.stringify($scope.pageSetting.dataGrids[$scope.name].sort)) {
                            $scope.gridOptions.sortInfo = $scope.pageSetting.dataGrids[$scope.name].sort;
                            $scope.updateSorting($scope.gridOptions.sortInfo, false);
                            changing = true;
                        }

                        if (JSON.stringify($scope.gridOptions.pagingOptions) != JSON.stringify($scope.pageSetting.dataGrids[$scope.name].paging)) {
                            $scope.gridOptions.pagingOptions = $scope.pageSetting.dataGrids[$scope.name].paging;
                            $scope.updatePaging($scope.gridOptions.pagingOptions, false);
                            changing = true;
                        }

                        if (changing) {
                            $scope.datasource.query();
                        }

                    }
                }

                // Generate Excel Template
                $scope.getCols = function (return_array) {
                    var cols = [];
                    if ($scope.columns) {
                        for (i in  $scope.columns) {
                            if ($scope.columns[i].visible === false)
                                continue;

                            if (!return_array) {
                                cols.push({idx: i, label: $scope.columns[i].label, name: $scope.columns[i].name});
                            } else {
                                cols.push($scope.columns[i].label);
                            }
                        }
                    } else if ($scope.datasource.data) {
                        for (i in $scope.datasource.data[0]) {
                            if (!return_array) {
                                cols.push({label: i, name: i});
                            } else {
                                cols.push(i);
                            }
                        }
                    }
                    return cols;
                };

                $scope.generateTemplate = function () {
                    var cols = $scope.getCols(true);

                    location.href = Yii.app.createUrl('/formfield/DataGrid.generateExcelTemplate', {
                        columns: JSON.stringify(cols)
                    });
                };

                // Export Excel
                $scope.exportExcel = function () {
                    var url = Yii.app.createUrl('/formfield/DataGrid.exportExcel');
                    var file = $scope.form.title;
                    var cols = $scope.getCols();
                    var data = [];
                    for (i in $scope.datasource.data) {
                        var row = $scope.datasource.data[i];
                        var d = {};
                        for (k in cols) {
                            var col = cols[k];

                            if (row[col.name + "_label"] && $scope.columns[col.idx].columnType == "relation") {
                                d[col.label] = row[col.name + "_label"];
                            } else {
                                d[col.label] = row[col.name];
                            }
                        }
                        data.push(d);
                    }
                    var data = JSON.stringify(data);

                    var form = $('<form target="_blank" action="' + url + '" method="post">' +
                            '<input type="hidden" name="data" value=\'' + data + '\' />' +
                            '<input type="hidden" name="file" value="' + file + '" />' +
                            '</form>');

                    $('body').append(form);
                    form.submit();
                    form.remove();
                };

                // Import Excel
                $scope.ext = function (file) {
                    var type = '';
                    if (typeof file.name == 'string') {
                        type = file.name.split('.');
                    }
                    if (typeof file == 'string') {
                        type = file.split('.');
                    }

                    if (type.length === 1 || (type[0] === "" && type.length === 2)) {
                        return "";
                    }
                    return type.pop().toLowerCase();
                };
                $scope.importIcon = "fa-upload";
                $scope.importProgress = 0;
                $scope.loadExcel = function ($files) {
                    if ($scope.datasource.data.length > 1) {
                        if (!confirm("Data will be replaced, are you sure ?")) {
                            return false;
                        }
                    }

                    if ($files.length > 0) {
                        var file = $files[0];
                        var ext = $scope.ext(file);
                        if (['xls', 'xlsx'].indexOf(ext) < 0) {
                            return false;
                        }
                        $scope.importIcon = "faa-flash animated fa-upload";

                        $upload.upload({
                            url: Yii.app.createUrl('/formfield/DataGrid.upload', {
                                a: 'excel'
                            }),
                            file: file
                        }).progress(function (evt) {
                            $scope.importProgress = parseInt(100.0 * evt.loaded / evt.total);
                        }).success(function (data, html) {
                            $scope.importProgress = 0;
                            $scope.importIcon = "fa-upload";
                            $scope.datasource.data = data;
                        }).error(function (data) {
                            $scope.importProgress = 0;
                            $scope.importIcon = "fa-upload";
                            alert(data);
                        });
                    }
                };

                // Type: String
                $scope.generateCellString = function (col) {
                    var format = "";
                    var placeholder = "";
                    var placeholderHtml = "";
                    var emptyVal = "['']";
                    switch (col.inputMask) {
                        case "99/99/9999 99:99":
                            placeholder = "dd/mm/yyyy hh:mm";
                            format = " | dateFormat:'d/M/Y H:i'";
                            emptyVal = "['','0000-00-00 00:00','0000-00-00', '00:00']";
                            break;
                        case "99/99/9999":
                            placeholder = "dd/mm/yyyy";
                            format = " | dateFormat:'d/M/Y'";
                            emptyVal = "['','0000-00-00 00:00','0000-00-00', '00:00']";
                            break;
                        case "99:99":
                            placeholder = "hh:mm";
                            format = " | hourFormat";
                            emptyVal = "['','0000-00-00 00:00','0000-00-00', '00:00']";
                            break;
                        case 'number':
                            format = " | number:2";
                    }

                    var showPlaceholder = $scope.gridOptions.enableCellEdit || $scope.gridOptions.enableExcelMode;
                    if (placeholder != "" && showPlaceholder) {
                        placeholderHtml = '<div ng-if="' +
                                emptyVal + '.indexOf(row.getProperty(col.field)) >=0 " style="color:#999">' +
                                placeholder + '</div>';
                    }
                    var varDef = 'row.getProperty(col.field)';
                    if (Object.prototype.toString.call(col.stringAlias) == "[object Object]") {
                        varDef = 'stringAlias(row.getProperty(col.field),col.field)';
                    }
                    if (!!col.options && !!col.options['parse-func']) {
                        varDef = col.options['parse-func'] + '(row.getProperty(col.field),row)';
                    }


                    if (!!col.options && !!col.options.cellFilter) {
                        varDef += ' | ' + col.options.cellFilter;
                    }

                    if (!!col.options && !!col.options.href) {
                        varDef = 'generateUrlLink(' + varDef + ', col.colDef, row)';
                    }

                    var ngIf = 'ng-if="' + emptyVal + '.indexOf(row.getProperty(col.field)) < 0 "';
                    var editableClass = $scope.getEditableClass(col);

                    var html = '<div class="ngCellText ' + editableClass + '" ng-class="col.colIndex()">\
                                <span ' + ngIf + ' ng-cell-text ng-bind-html="' + varDef + ' ' + format + '"></span>\
                                ' + placeholderHtml + '\
                                </div>';
                    return html;
                }
                $scope.generateEditString = function (col) {
                    var uimask = "";

                    var placeholder = "";
                    switch (col.inputMask) {
                        case "99/99/9999 99:99":
                            placeholder = "placeholder='dd/mm/yyyy hh:mm'";
                            uimask = "ui-mask='" + col.inputMask + "'";
                            break;
                        case "99/99/9999":
                            placeholder = "placeholder='dd/mm/yyyy'";
                            uimask = "ui-mask='" + col.inputMask + "'";
                            break;
                        case "99:99":
                            placeholder = "placeholder='hh:mm'";
                            uimask = "ui-mask='" + col.inputMask + "'";
                            break;
                    }

                    var html = '<input ' + uimask + ' ' + placeholder + ' ng-class="\'colt\' + col.index" \
                                ng-input="COL_FIELD"  ng-model="COL_FIELD" />';
                    return html;
                }

                // Type: Button
                $scope.generateButtons = function (column) {
                    var buttons = column.buttons;
                    var editable = $scope.getEditableClass(column);

                    var html = '<div class="ngCellButton colt{{$index}} ' + editable + '">';
                    var btnSize = 'btn-xs';

                    if (column.buttonCollapsed == 'Yes') {
                        btnSize = 'btn-sm';
                        html += '<div class="ngCellButtonCollapsed">';
                        html += '<div class="ngCellButtonCollapsedDetail">';
                    }

                    column.calculatedWidth = 22;
                    for (i in buttons) {
                        var b = buttons[i];
                        var opt = b.options || {};
                        var tag = "div";
                        var label = b.label;
                        var icon = "";
                        var attr = [];

                        // generate attribute
                        opt['ng-click'] = 'buttonClick(row, $event)';
                        opt.class = (opt.class || '') + ' btn ' + btnSize + ' btn-default';

                        if (typeof opt.href != "undefined") {
                            if (opt.href.substr(0, 4) == "url:") {
                                var url = opt.href.substr(4);
                                opt.href = '{{' + $scope.generateUrl(url) + '}}';
                            }
                            tag = "a";
                        }

                        if (b.icon != "") {
                            icon = '<i class="' + b.icon + '"></i>';
                        }

                        for (i in opt) {
                            attr.push(i + '="' + opt[i] + '"');
                        }

                        // create html
                        html += '<' + tag + ' ' + attr.join(' ') + '>' + icon + ' ' + label + '</' + tag + '>';

                        column.calculatedWidth += 30 + (icon == "" ? 0 : 12) + label.length * 4;
                    }

                    if (column.buttonCollapsed == 'Yes') {
                        html += '</div>';
                        html += '<span>...</span></div>';
                    }

                    html += '</div>';
                    return html;
                }

                // Type: Dropdown
                $scope.generateDropdown = function (col) {
                    var id = $scope.name + '-' + col.name + '-dropdownlist';

                    if (col.listType == 'js') {
                        col.listItem = JSON.stringify(parent.$eval(col.listExpr));
                    }
                    $('<div id="' + id + '" style="display:none;">' + col.listItem + '</div>').appendTo('body');

                    var html = '<input';
                    html += ' dg-autocomplete dga-id="' + id + '" dga-must-choose="' + col.listMustChoose + '"';
                    html += ' type="text" ng-class="\'colt\' + col.index"';
                    html += ' ng-input="COL_FIELD" ng-model="COL_FIELD" />';

                    return html;
                }

                // Type: Relation
                $scope.generateEditRelation = function (col) {
                    var relParams = [];
                    if (typeof JSON.stringify(col.relParams) == 'undefined') {
                        relParams = [];
                    } else {
                        relParams = JSON.stringify(col.relParams).replace(/[']/g, "�q�");
                    }
                    var html = '<input';
                    html += ' dg-relation params=\'' + relParams + '\'';
                    html += ' type="text" ng-class="\'colt\' + col.index"';
                    html += ' ng-input="COL_FIELD_label" ng-model="COL_FIELD_label" />';

                    return html;
                }
                $scope.generateCellRelation = function (col) {
                    var editableClass = $scope.getEditableClass(col);


                    var emptyString = '- Empty -';
                    var emptyStyle = 'color:#999;font-size:12px;';
                    if (col.options && col.options['if-empty']) {
                        emptyString = col.options['if-empty'];
                        emptyStyle = '';
                    }

                    var html = '<div class="ngCellText dgr ' + editableClass + '" ng-class="col.colIndex()"';
                    html += 'dgr-id="{{row.getProperty(col.field)}}" dgr-model="' + col.relModelClass + '" ';
                    html += 'dgr-class="' + $scope.modelClass + '" dgr-name="' + $scope.name + '" dgr-col="' + col.name + '" dgr-labelField="' + col.relLabelField + '" ';
                    html += 'dgr-idField="' + col.relIdField + '">';
                    html += '<span ng-if=\'row.getProperty(col.field + "_label") == ""\' style="' + emptyStyle + '">' + emptyString + '</span>';
                    html += '<span ng-if=\'row.getProperty(col.field + "_label") != ""\' ng-cell-text>{{row.getProperty(col.field + "_label")}}';
                    html += '</span></div>';

                    return html;
                }

                $scope.initGrid = function () {
                    $scope.grid = this;
                }

                $scope.fillColumns = function () {
                    $timeout(function () {
                        var columns = [];
                        $scope.datasource = parent[$el.find("data[name=datasource]").text()];

                        if (typeof $scope.datasource != "undefined") {
                            $scope.data = $scope.datasource.data;
                        } else {
                            $scope.data = [];
                        }

                        // prepare gridOptions
                        evalArray($scope.gridOptions);

                        $scope.gridOptions.data = 'data';
                        $scope.gridOptions.plugins = [new ngGridFlexibleHeightPlugin(), new anchorLastColumn()];
                        $scope.gridOptions.headerRowHeight = 28;
                        $scope.gridOptions.rowHeight = 28;
                        $scope.gridOptions.multiSelect = $scope.gridOptions.multiSelect || false;
                        $scope.gridOptions.enableColumnResize = $scope.gridOptions.enableColumnResize === false ? false : true;

                        if ($scope.gridOptions.generateColumns || (
                                !!$scope.data && !!$scope.data.length &&
                                $scope.columns !== null &&
                                $scope.data.length > 0 &&
                                $scope.columns.length == 0)) {

                            $scope.availableCols = [];
                            for (i in $scope.columns) {
                                $scope.availableCols.push($scope.columns[i].name);
                            }
                            for (i in $scope.data[0]) {
                                if ($scope.availableCols.indexOf(i) < 0) {
                                    $scope.columns.push({
                                        label: i,
                                        name: i,
                                        columnType: "string",
                                        options: {}
                                    });
                                }
                            }

                            if ($scope.gridOptions.generateColumns && $scope.data.length == 0) {
                                $scope.columns.length = 0;
                            }
                        }

                        if ($scope.gridOptions.groups) {
                            for (i in $scope.columns) {
                                var c = $scope.columns[i];
                                if ($scope.gridOptions.groups.indexOf(c.name) >= 0) {
                                    if (!c.options.visible) {
                                        c.options.visible = false;
                                    }
                                }
                            }
                        }

                        if (typeof $scope.onBeforeLoaded == 'function') {
                            $scope.onBeforeLoaded($scope);
                        }

                        // prepare ng-grid columnDefs
                        var buttonID = 1;
                        for (i in $scope.columns) {
                            var c = $scope.columns[i];

                            // prepare columns
                            evalArray(c.options, {parseUrl: false});
                            switch (c.columnType) {
                                case "string":
                                    var col = angular.extend(c.options || {}, {
                                        field: c.name,
                                        displayName: c.label,
                                        cellTemplate: $scope.generateCellString(c),
                                        editableCellTemplate: $scope.generateEditString(c)
                                    });
                                    break;
                                case "buttons":
                                    var col = angular.extend(c.options || {}, {
                                        field: 'button_' + buttonID,
                                        displayName: c.label,
                                        enableCellEdit: false,
                                        sortable: false,
                                    });
                                    col.cellTemplate = $scope.generateButtons(c);
                                    col.width = c.options.width || c.calculatedWidth;

                                    buttonID++;
                                    break;
                                case "dropdown":
                                    var col = angular.extend(c.options || {}, {
                                        field: c.name,
                                        displayName: c.label,
                                        cellTemplate: $scope.generateCell(c),
                                        editableCellTemplate: $scope.generateDropdown(c)
                                    });
                                    break;
                                case "relation":
                                    var col = angular.extend(c.options || {}, {
                                        field: c.name,
                                        displayName: c.label,
                                        cellTemplate: $scope.generateCellRelation(c),
                                        editableCellTemplate: $scope.generateEditRelation(c)
                                    });
                                    break;
                            }

                            if (!col.width && columns.length > 11) {
                                col.width = Math.max(col.displayName.length * 12, 50);
                            }
                            columns.push(col);
                        }


                        if (columns.length > 0) {
                            $scope.gridOptions.columnDefs = columns;
                        }

                        // pagingOptions
                        $scope.updatePaging = function (paging, executeQuery, oldpaging) {
                            var ds = $scope.datasource;
                            ds.updateParam('currentPage', paging.currentPage, 'paging');
                            ds.updateParam('pageSize', paging.pageSize, 'paging');
                            ds.updateParam('totalServerItems', paging.totalServerItems, 'paging');
                            ds.lastQueryFrom = "DataGrid";

                            if (typeof executeQuery == "undefined") {
                                executeQuery = true;
                            }
                            if (executeQuery) {
                                if (typeof oldpaging != "undefined" && paging.pageSize == oldpaging.pageSize) {
                                    ds.queryWithoutCount();
                                } else {
                                    ds.query();
                                }
                                $scope.savePageSetting();
                            }
                        }

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
                                    var maxPage = Math.ceil($scope.datasource.totalItems / $scope.gridOptions.pagingOptions.pageSize);

                                    if (isNaN($scope.gridOptions.pagingOptions.currentPage) || $scope.gridOptions.pagingOptions.currentPage == '') {
                                        $scope.gridOptions.pagingOptions.currentPage = 1;
                                    }

                                    if ($scope.gridOptions.pagingOptions.currentPage > maxPage) {
                                        $scope.gridOptions.pagingOptions.currentPage = maxPage;
                                    }

                                    if (typeof $scope.datasource != "undefined") {
                                        if (timeout != null) {
                                            clearTimeout(timeout);
                                        }
                                        timeout = setTimeout(function () {

                                            $scope.updatePaging(paging, true, oldpaging);
                                        }, 100);
                                    }
                                }
                            }, true);
                        }

                        // sortOptions
                        $scope.updateSorting = function (sort, executeQuery) {
                            var ds = $scope.datasource;
                            if (typeof ds != "undefined") {

                                var order_by = [];
                                for (i in sort.fields) {
                                    order_by.push({
                                        field: sort.fields[i],
                                        direction: sort.directions[i]
                                    });
                                }


                                ds.lastQueryFrom = "DataGrid";
                                ds.updateParam('order_by', order_by, 'order');

                                if (typeof executeQuery == "undefined") {
                                    executeQuery = true;
                                }
                                if (executeQuery) {
                                    ds.queryWithoutCount();
                                    $scope.savePageSetting();
                                }
                            }
                        }

                        if ($scope.gridOptions['useExternalSorting']) {
                            $scope.gridOptions.sortInfo = {
                                columns: [],
                                fields: [],
                                directions: []
                            };

                            $scope.$watch('gridOptions.sortInfo', function (sort, oldsort) {
                                if (sort != oldsort) {
                                    $scope.updateSorting(sort);
                                }
                            }, true);
                        }

                        // fixedHeader
                        if ($scope.gridOptions['fixedHeader'] !== false) {
                            $timeout(function () {

                                var $container = $el.parents('.container-full');
                                var $dgcontainer = $el.find(".data-grid-container");
                                var $pager = $el.find(".data-grid-paging");
                                var $cat = $el.find('.data-grid-category');
                                var $catt = $el.find('.data-grid-category .ngTopPanel');
                                var $topp = $el.find('.data-grid-table .ngTopPanel');
                                var $container = $el.parents('.container-full');
                                var $wc = $el.parent();
                                var $form = $el.parents("form");
                                var formTopPos = Math.abs($form.position().top - $form.offset().top);
                                var formTop = $form.offset().top;
                                var pagerTop = $pager.length > 0 ? $pager.offset().top : 0;
                                var pagerHeight = $pager.length > 0 ? $pager.height() : 0;
                                var top = Math.abs(pagerTop - formTop);
                                var adjTop = 10;

                                if (!$scope.gridOptions['enableExcelMode'] && !$scope.gridOptions['enableCellEdit'] && !$scope.gridOptions['enablePaging']) {
                                    adjTop = 0;
                                }

                                function fixHead() {
                                    var width = $wc.width();
                                    $catt.width(width);

                                    if (($container.scrollTop() > top) || $scope.gridOptions['fixedHeader'] == "always") {
                                        if (!$dgcontainer.hasClass('fixed')) {
                                            $dgcontainer.addClass('fixed');
                                        }

                                        $pager.width(width);
                                        $pager.css('top', formTopPos);

                                        $cat.width(width);
                                        $cat.css('top', formTopPos + pagerHeight + adjTop);

                                        $topp.width(width);
                                        $topp.css('top', formTopPos + pagerHeight + $cat.height() + adjTop);
                                        $el.find(".data-grid-paging-shadow").show();
                                    } else {
                                        if ($dgcontainer.hasClass('fixed')) {
                                            $dgcontainer.removeClass('fixed');
                                        }
                                        $pager.attr('style', '');
                                        $cat.attr("style", '');
                                        $topp.attr("style", '');
                                        $catt.css('margin-top', '0px');
                                        $el.find(".data-grid-paging-shadow").hide();
                                    }
                                }

                                $(window).resize(fixHead);
                                $container.scroll(fixHead);
                                fixHead();
                            }, 0);
                        }

                        // excelMode
                        if ($scope.gridOptions['enableExcelMode']) {
                            $scope.gridOptions['enableCellEdit'] = true;
                            $scope.gridOptions['enableCellSelection'] = true;
                            $scope.gridOptions['afterSelectionChange'] = $scope.excelModeSelChange;
                            $scope.lastFocus = null;

                            var emec = [];
                            if ($scope.gridOptions['excelModeExcludeColumns']) {
                                emec = $scope.$eval($scope.gridOptions['excelModeExcludeColumns']);
                            }
                            for (i in emec) {
                                $scope.datasource.untrackColumns.push(emec[i]);
                            }

                            var excludeColumns = function (data) {
                                var except = [];
                                var cols = [];

                                for (i in $scope.columns) {
                                    if (typeof $scope.columns[i].visible == "undefined" || $scope.columns[i].visible) {
                                        cols.push($scope.columns[i].name);
                                    }
                                }

                                for (i in data) {
                                    if (cols.indexOf(i) < 0) {
                                        except.push(i);
                                    }
                                }

                                for (i in emec) {
                                    except.push(emec[i]);
                                }

                                return except;
                            };

                            $(window).on('focus', function () {
                                if ($scope.lastFocus != null) {
                                    $scope.lastFocus.focus();
                                }
                            });

                            $el.parents('form').submit(function (e) {
                                if ($scope.data.length > 0 || !$el.attr('gridReadyToSubmit')) {
                                    var except = excludeColumns($scope.data[0]);
                                    var newData = [];
                                    var idx = 0;
                                    for (i in $scope.data) {
                                        var row = $scope.data[i];
                                        if ($scope.isNotEmpty(row, except)) {
                                            newData.push(row);
                                        }
                                        idx++;
                                    }

                                    $timeout(function () {
                                        $scope.datasource.data = newData;
                                    });
                                }
                            });

                            $el.on('focus', '[ng-cell] div', function () {
                                $scope.lastFocus = $(this);
                            });

                            $scope.$on('ngGridEventEndCellEdit', function (evt) {
                                var row = evt.targetScope.row;
                                var data = row.entity;
                                var except = excludeColumns(data);

                                if ($scope.data.length - 1 == row.rowIndex) {
                                    $timeout(function () {
                                        $scope.addRow(row);
                                    }, 0);
                                }
                            });

                            $timeout(function () {
                                if (typeof $scope.data == "undefined" || $scope.data.length == 0) {
                                    $scope.addRow();
                                } else {
                                    var except = excludeColumns($scope.data[0]);
                                    if ($scope.isNotEmpty($scope.data[$scope.data.length - 1], except)) {
                                        $scope.addRow();
                                    }
                                }
                            }, 0);
                        }

                        var dgr = {};
                        var dgrCols = [];
                        //load relation
                        $timeout(function () {
                            function countDgr() {
                                dgrCols = [];
                                $el.find(".dgr").each(function () {
                                    var model = $(this).attr('dgr-model');
                                    var id = $(this).attr('dgr-id');
                                    var name = $(this).attr('dgr-name');
                                    var cls = $(this).attr('dgr-class');
                                    var col = $(this).attr('dgr-col');
                                    var labelField = $(this).attr('dgr-labelField');
                                    var idField = $(this).attr('dgr-idField');

                                    if (dgrCols.indexOf(name) < 0) {
                                        dgrCols.push({
                                            name: col,
                                            model: model,
                                            labelField: labelField,
                                            idField: idField
                                        });
                                    }

                                    dgr['name'] = name;
                                    dgr['class'] = cls;
                                    dgr['cols'] = dgr['cols'] || {};
                                    dgr['cols'][col] = dgr['cols'][col] || [];

                                    if (!!$scope.model && !!$scope.model.id) {
                                        dgr['model_id'] = $scope.model.id;
                                    }

                                    if (id != "" && dgr['cols'][col].indexOf(id) < 0) {
                                        dgr['cols'][col].push(id);
                                    }
                                });
                            }


                            var url = Yii.app.createUrl('/formfield/RelationField.dgrInit');

                            function loadRelation(callback) {
                                countDgr();
                                if (dgrCols.length > 0) {
                                    $scope.loading = true;
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

                                            if (typeof callback == "function") {
                                                callback();
                                            }
                                        }
                                        $scope.loading = false;
                                    });
                                }
                            }

                            $timeout(function () {
                                loadRelation();
                            }, 100);

                            $scope.datasource.beforeQueryInternal[$scope.renderID] = function () {
                                if ($scope.datasource.lastQueryFrom == "DataFilter" && !!$scope.gridOptions.pagingOptions) {
                                    $scope.gridOptions.pagingOptions.currentPage = 1;
                                }
                            }
                            $scope.datasource.afterQueryInternal[$scope.renderID] = function () {
                                $timeout(function () {
                                    loadRelation();
                                });
                            };
                        }, 100);

                        if (typeof $scope.onGridLoaded == 'function') {
                            $scope.onGridLoaded($scope.gridOptions);
                        }
                        $scope.loaded = true;

                        $scope.loadPageSetting();
                    }, 0);
                }

                $scope.$watch('datasource.data', function () {
                    if ($scope.datasource != null) {
                        $scope.data = $scope.datasource.data;
                    }
                });

                $scope.reset = function () {
                    $scope.resetPageSetting();
                    location.reload();
                }

                $scope.Math = window.Math;
                $scope.grid = null;
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.modelClass = $el.find("data[name=model_class]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.gridOptions = JSON.parse($el.find("data[name=grid_options]").text());
                $scope.columns = JSON.parse($el.find("data[name=columns]").text());
                $scope.loaded = false;
                $scope.onGridLoaded = '';
                $scope.fillColumns();
                parent[$scope.name] = $scope;
                
                // this is hack to prevent datagrid is fully loaded before page load
                // check FormBuilder.js.php for more info
                parent.dataGrids[$scope.name] = true;
                parent.dataGrids.length--; 
            }
        }
    };
});