(function (Handsontable) {
    
    /*************** DROPDOWN TYPE *******************/
    function dropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
        var $scope = cellProperties.$scope;
        var cp = $scope.columns[col];

        if (!!cp.defaultValue) {
            if (!value) {
                value = cp.defaultValue;
                $scope.updateDSCell(row, prop, value);
            }
        }

        if (typeof value == 'string') {
            value = value.trim();
        }

        var idx = cp.sourceValues.indexOf(value);
        if (idx < 0) {
            idx = cp.source.indexOf(value);
            if (idx >= 0) {
                value = cp.sourceValues[idx];
                $scope.updateDSCell(row, prop, value);
            }
        }
        value = cp.source[idx];

        Handsontable.AutocompleteCell.renderer.apply(this, arguments);
        return td;
    }
    Handsontable.renderers.registerRenderer('dtDropdown', dropdownRenderer);


    var DropdownEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();
    DropdownEditor.prototype.prepare = function () {
        Handsontable.editors.AutocompleteEditor.prototype.prepare.apply(this, arguments);
        this.cellProperties.filter = false;
        this.cellProperties.strict = true;
    };
    DropdownEditor.prototype.open = function () {
        var $scope = this.cellProperties.$scope;
        var cp = $scope.columns[this.col];
        var value = $scope.data[this.row][this.prop];
        var idx = cp.sourceValues.indexOf(value);
        if (idx < 0) {
            idx = cp.source.indexOf(value);
            if (idx >= 0) {
                value = cp.sourceValues[idx];
                $scope.updateDSCell(this.row, this.prop, value);
            }
        }
        value = cp.source[idx];
        this.TEXTAREA.value = (!!value) ? value : '';
        this.cellProperties.opened = true;
        Handsontable.editors.AutocompleteEditor.prototype.open.apply(this, arguments);
    };
    DropdownEditor.prototype.close = function () {
        this.cellProperties.opened = false;
        Handsontable.editors.AutocompleteEditor.prototype.close.apply(this, arguments);
    };
    Handsontable.editors.dtDropdownEditor = DropdownEditor;
    Handsontable.editors.registerEditor('dtDropdown', DropdownEditor);
    

    /*************** DROPDOWN TYPE *******************/
    function dropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
        var $scope = cellProperties.$scope;
        var cp = $scope.columns[col];

        if (!!cp.defaultValue) {
            if (!value) {
                value = cp.defaultValue;
                $scope.updateDSCell(row, prop, value);
            }
        }

        if (typeof value == 'string') {
            value = value.trim();
        }

        var idx = cp.sourceValues.indexOf(value);
        if (idx < 0) {
            idx = cp.source.indexOf(value);
            if (idx >= 0) {
                value = cp.sourceValues[idx];
                $scope.updateDSCell(row, prop, value);
            }
        }
        value = cp.source[idx];

        Handsontable.AutocompleteCell.renderer.apply(this, arguments);
        return td;
    }
    Handsontable.renderers.registerRenderer('dtDropdown', dropdownRenderer);


    var DropdownEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();
    DropdownEditor.prototype.prepare = function () {
        Handsontable.editors.AutocompleteEditor.prototype.prepare.apply(this, arguments);
        this.cellProperties.filter = false;
        this.cellProperties.strict = true;
    };
    DropdownEditor.prototype.open = function () {
        var $scope = this.cellProperties.$scope;
        var cp = $scope.columns[this.col];
        var value = $scope.data[this.row][this.prop];
        var idx = cp.sourceValues.indexOf(value);
        if (idx < 0) {
            idx = cp.source.indexOf(value);
            if (idx >= 0) {
                value = cp.sourceValues[idx];
                $scope.updateDSCell(this.row, this.prop, value);
            }
        }
        value = cp.source[idx];
        this.TEXTAREA.value = (!!value) ? value : '';
        this.cellProperties.opened = true;
        Handsontable.editors.AutocompleteEditor.prototype.open.apply(this, arguments);
    };
    DropdownEditor.prototype.close = function () {
        this.cellProperties.opened = false;
        Handsontable.editors.AutocompleteEditor.prototype.close.apply(this, arguments);
    };
    Handsontable.editors.dtDropdownEditor = DropdownEditor;
    Handsontable.editors.registerEditor('dtDropdown', DropdownEditor);
    /*************** RELATION TYPE *******************/
    function relationRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.AutocompleteCell.renderer.apply(this, arguments);
        return td;
    }
    Handsontable.renderers.registerRenderer('relation', relationRenderer);

    var RelationEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();
    RelationEditor.prototype.open = function () {
        this.cellProperties.opened = true;
        this.TEXTAREA.value = '';
        Handsontable.editors.AutocompleteEditor.prototype.open.apply(this, arguments);
    };
    RelationEditor.prototype.close = function () {
        this.cellProperties.opened = false;
        Handsontable.editors.AutocompleteEditor.prototype.close.apply(this, arguments);
    };
    RelationEditor.prototype.updateDS = function ($scope, row, col, value) {
        var c = [row, col, '', value];
        if ($scope.dtGroups) {
            $scope.dtGroups.handleChange($scope, c);
        } else {
            if (!$scope.datasource.data[c[0]]) {
                $scope.datasource.data[c[0]] = {};
            }
            $scope.datasource.data[c[0]][c[1]] = c[3];
        }
    }
    RelationEditor.prototype.checkRel = function (value, callback) {
        var $scope = this.$scope;
        var relList = this.relList;

        if (typeof relList[value] == "undefined") {
            var name = this.name;
            var originalRow = this.row;
            if (!$scope.ht || !$scope.ht.getSelected) {
                callback(false);
                return false;
            }
            var s = $scope.ht.getSelected();
            if (!s) {
                callback(false);
                return false;
            }

            var row = s[0];
            var col = s[1];

            var opt = $scope.columns[col];
            $scope.$http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                's': value,
                'm': $scope.modelClass,
                'f': $scope.name,
                'c': name,
                'p': opt.relParams
            }).success(function (data) {
                // cache query
                var labels = [];
                for (i in data) {
                    if (!data[i].label)
                        continue;

                    labels.push(data[i].label);
                    relList[data[i].label.trim('"')] = data[i].value;
                }

                if (labels.indexOf(value) >= 0) {
                    callback(true);
                } else {
                    RelationEditor.prototype.updateDS($scope, originalRow, name, '');
                    $scope.data[originalRow][name] = '';
                    callback(false);
                }
            });
        } else {
            RelationEditor.prototype.updateDS($scope, this.row, this.name, relList[value]);
            $scope.data[this.row][this.name] = relList[value];
            callback(true);
        }
    }
    RelationEditor.prototype.search = function (query, process) {
        if (!this.opened) {
            process([]);
            return;
        }

        var $scope = this.$scope;
        var $q = $scope.$q;
        var $http = $scope.$http;
        if (!$scope.triggerRelationWatch) {
            return false;
        }
        var relList = this.relList;
        var s = this.instance.getSelected();
        if (s) {
            var row = s[0];
            var col = s[1];
            var opt = $scope.columns[col];
            if ($scope.dtGroups && $scope.data[row]['__dt_flg'] == "G") {
                var prop = $scope.dtGroups.groupCols[$scope.data[row]['__dt_lvl']];
                opt = $scope.dtGroups.groupColOpts[prop + $scope.relSuffix];
            }

            if (opt.columnType != "relation")
                return;
            for (i in opt.relParams) {
                var p = opt.relParams[i];
                if (p.indexOf('js:') === 0) {
                    var value = $scope.$eval(p.replace('js:', ''));
                    opt.relParams[i] = value;
                }
            }
            if ($scope.httpRequest) {
                $scope.httpRequest.resolve();
            }
            $scope.httpRequest = $q.defer();
            $http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                's': query,
                'm': $scope.modelClass,
                'f': $scope.name,
                'c': opt.name,
                'p': opt.relParams
            }, {
                timeout: $scope.httpRequest.promise
            }).success(function (data) {
                // cache query
                var labels = [];
                for (i in data) {
                    if (!data[i].label)
                        continue;

                    labels.push(data[i].label);
                    relList[data[i].label.trim('"')] = data[i].value;
                }

                if (labels.length && labels.length > 0) {
                    process(labels);
                }
            });
        }
    }
    Handsontable.editors.RelationEditor = RelationEditor;
    Handsontable.editors.registerEditor('relation', RelationEditor);

    /*************** DATE TIME  *******************/
    function formatDate(val, format, $filter, td) {
        var oldval = val;
        if (typeof oldval != "string")
            oldval = "";

        if (typeof val == "string") {
            var t = val.split(/[- :]/);
            if (t.length > 3) {
                val = new Date(t[0], t[1] - 1, t[2], t[3], t[4]);
            } else if (t.length == 3) {
                if (val.indexOf(':') > 0) {
                    val = new Date(1, 0, 0, t[0], t[1], t[2]);
                } else {
                    val = new Date(t[0], t[1] - 1, t[2], 0, 0);
                }
            } else if (t.length == 2) {
                val = new Date();
                val.setMinutes(t[0]);
                val.setMinutes(t[1]);
            }
            if (val == "Invalid Date" || (typeof val == 'object' && val.getFullYear() < 1900)) {
                val = "";
            }
        } else {
            val = "";
        }

        switch (format) {
            case "99/99/9999":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy'));
                } else {
                    if (oldval.split(/\//).length == 3)
                        return oldval;

                    if (td) {
                        val = "dd/mm/yyyy";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99/99/9999 99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'dd/MM/yyyy HH:mm'));
                } else {

                    if (td) {
                        val = "dd/mm/yyyy hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
            case "99:99":
                if (val != "") {
                    val = ($filter('date')(val, 'HH:mm'));
                } else {

                    if (td) {
                        val = "hh:mm";
                        $(td).css("color", "#999");
                    }
                }
                break;
        }
        return val;
    }

    function dateTimeRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);
        var options = instance.getSettings().columns[col];

        value = cellProperties.$scope.formatDateToSql(value, options.inputMask);
        Handsontable.Dom.fastInnerHTML(td, formatDate(value, options.inputMask, options.filter, td));
        return td;
    }

    Handsontable.renderers.registerRenderer('datetime', dateTimeRenderer);

    /*************** CHECKBOX RENDERER *******************/
    function dtCheckboxRenderer(instance, td, row, col, prop, value, cellProperties) {
        var $scope = cellProperties.$scope;
        td.className = 'cbCell ';
        if (cellProperties.isGroup) {
            td.className += ' groups';
        }

        if (cellProperties.groupOnly) {
            td.className += ' cbGroupOnly';
        }

        if (prop == $scope.cbSuffix)
            return td;

        var originalVal = false;
        if (typeof $scope.data[row] != "undefined") {
            originalVal = $scope.data[row][cellProperties.dataOri];
        }

        var checked = cellProperties.checked;
        var checkedRow = cellProperties.checkedRow;
        var checkedGroup = cellProperties.checkedGroup;
        var eventManager = Handsontable.eventManager(instance);

        if (!cellProperties.isGroup) {
            if (checkedRow.indexOf(cellProperties.row) >= 0) {
                value = cellProperties.checkedTemplate;
            } else {
                if (originalVal == cellProperties.checkedTemplate && !cellProperties.init) {
                    checkedRow.push(row);
                    if (checked.indexOf(originalVal) < 0) {
                        checked.push(originalVal);
                    }
                    value = cellProperties.checkedTemplate;
                    cellProperties.init = true;
                } else {
                    value = cellProperties.uncheckedTemplate;
                }
            }
        } else {
            if (checkedGroup.indexOf(cellProperties.row) >= 0) {
                value = cellProperties.checkedTemplate;
            } else {
                value = cellProperties.uncheckedTemplate;
            }
        }

        function toggle(el, ischecked) {
            cellProperties.init = true;
            if (!$(el).hasClass('groups')) {
                var val = $scope.data[row][cellProperties.dataOri];
                var vidx = checked.indexOf(originalVal);
                var idx = checkedRow.indexOf(cellProperties.row);
                var result = null;
                if (idx >= 0) {
                    checkedRow.splice(idx, 1);
                    if (vidx >= 0) {
                        var shouldSplice = true;
                        for (cr in checkedRow) {
                            if ($scope.data[cr][cellProperties.dataOri] == originalVal) {
                                shouldSplice = false;
                            }
                        }

                        if (shouldSplice) {
                            checked.splice(vidx, 1);
                        }
                    }
                    result = cellProperties.uncheckedTemplate;

                } else if (idx < 0) {
                    if (vidx < 0) {
                        if (cellProperties.changeValue) {
                            if (checked.indexOf(cellProperties.checkedTemplate) < 0) {
                                checked.push(cellProperties.checkedTemplate);
                            }
                        } else {
                            checked.push(originalVal);
                        }
                    }
                    checkedRow.push(cellProperties.row);
                    result = cellProperties.checkedTemplate;
                }

                instance.setDataAtRowProp(row, prop, result);

                if (cellProperties.changeValue) {
                    instance.setDataAtRowProp(row, cellProperties.dataOri, result);
                }

                return checkedRow.indexOf(val) >= 0;
            } else {
                var idx = checkedGroup.indexOf(cellProperties.row);
                var rows = $scope.dtGroups.findRows(cellProperties.$scope.data[cellProperties.row]);
                var groups = $scope.dtGroups.findGroupFlatten(cellProperties.$scope.data[cellProperties.row]);
                var col = prop.substr(0, prop.length - $scope.cbSuffix.length);
                var changes = [];

                groups.forEach(function (item, i) {
                    var gidx = checkedGroup.indexOf(item['__dt_idx']);
                    if (ischecked) {
                        if (gidx < 0) {
                            checkedGroup.push(item['__dt_idx']);
                        }
                    } else {
                        if (gidx >= 0) {
                            checkedGroup.splice(gidx, 1);
                        }
                    }
                });

                rows.forEach(function (item, i) {
                    if (ischecked) {
                        if (checked.indexOf(item[col]) < 0) {
                            if (cellProperties.changeValue) {
                                if (checked.indexOf(cellProperties.checkedTemplate) < 0) {
                                    checked.push(cellProperties.checkedTemplate);
                                }
                            } else {
                                checked.push(item[col]);
                            }
                        }
                        if (checkedRow.indexOf(item['__dt_idx']) < 0) {
                            checkedRow.push(item['__dt_idx']);
                        }
                    } else {
                        var checkedVidx = checkedRow.indexOf(item['__dt_idx']);
                        if (checkedVidx >= 0) {
                            checkedRow.splice(checkedVidx, 1);
                        }

                        var checkedIdx = checked.indexOf(item[col]);
                        if (checkedIdx >= 0) {
                            checked.splice(checkedIdx, 1);
                        }
                    }

                    var newval = ischecked ? cellProperties.checkedTemplate : cellProperties.uncheckedTemplate;
                    var oldval = !ischecked ? cellProperties.checkedTemplate : cellProperties.uncheckedTemplate;

                    changes.push([i, col + $scope.cbSuffix, oldval, newval]);
                    if (cellProperties.changeValue) {
                        if (item['__dt_flg'] == 'Z') {
                            rows[i][col] = newval;
                            rows[i][col + $scope.cbSuffix] = newval;
                        }
                        changes.push([i, col, oldval, newval]);
                    }
                });

                for (cr in checkedRow) {
                    var item = $scope.data[cr];
                    if (checked.indexOf(item[col]) < 0 && item['__dt_flg'] == "Z") {
                        checked.push(item[col]);
                    }
                }

                $scope.ht = $scope.getInstance();
                Handsontable.hooks.run($scope.ht, 'beforeChange', 'paste', changes);
                Handsontable.hooks.run($scope.ht, 'afterChange', 'paste', changes);
                $scope.ht.render();
                return checkedGroup.indexOf(cellProperties.row);
            }

        }

        Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
        eventManager.removeEventListener(td, 'mousedown');
        eventManager.addEventListener(td, 'mousedown', function (e) {
            toggle(this, !$(this).find("input").is(":checked"));
        });

        return td;
    }

    Handsontable.renderers.registerRenderer('dtCheckbox', dtCheckboxRenderer);

    /*************** STRING ALIAS RENDERER *******************/
    function stringAliasRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);
        var options = instance.getSettings().columns[col];
        var val = value;

        angular.forEach(options.stringAlias, function (alias, idx) {
            if (idx == value) {
                val = alias;
            }
        });

        if (typeof options.options.style == "string") {
            td.setAttribute('style', options.options.style);
        }
        Handsontable.Dom.fastInnerHTML(td, val);

        return td;
    }

    Handsontable.renderers.registerRenderer('stringalias', stringAliasRenderer);

    /*************** INPUT MASK RENDERER *******************/
    var InputMaskEditor = Handsontable.editors.TextEditor.prototype.extend();
    InputMaskEditor.prototype.createElements = function () {
        Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

        var row = this.instance.getSelected()[0];
        var col = this.instance.getSelected()[1];
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        this.TEXTAREA = document.createElement('input');
        this.TEXTAREA.setAttribute('type', 'text');
        this.TEXTAREA.className = 'handsontableInput';
        this.textareaStyle = this.TEXTAREA.style;
        this.textareaStyle.width = 0;
        this.textareaStyle.height = 0;
        this.$textarea = $(this.TEXTAREA);

        Handsontable.Dom.empty(this.TEXTAREA_PARENT);
        this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

        $(this.TEXTAREA)
                .bind('focus', function () {
                    var val = $(this).val();
                    $(this).val(formatDate(val, options.inputMask, options.filter));
                    $(this).mask(options.inputMask);
                })

    };

    InputMaskEditor.prototype.close = function () {
        var ed = this.instance.getActiveEditor();
        var row = ed.row;
        var col = ed.col;
        var instance = this.instance;
        var options = this.instance.getSettings().columns[col];

        var val = $(this.TEXTAREA).val();
        var $filter = options.filter;

        Handsontable.editors.TextEditor.prototype.close.apply(this, arguments);

        switch (options.inputMask) {
            case "99/99/9999":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99/99/9999 99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date(t[2], t[1] - 1, t[0], t[3], t[4]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
            case "99:99":
                var t = val.split(/[\/ :]/);
                var d = new Date();
                d.setHours(t[0]);
                d.setMinutes(t[1]);
                instance.setDataAtCell(row, col, $filter('date')(d, 'yyyy-MM-dd HH:mm'));
                break;
        }

    };
    Handsontable.editors.InputMaskEditor = InputMaskEditor;
    Handsontable.editors.registerEditor('mask', InputMaskEditor);

    /*************** GROUPS RENDERER *******************/
    function groupsRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);

        if (col == 0) {
            var row = cellProperties.$scope.data[row];
            var whitespace = "    ";
            if (row && !!row['__dt_flg']) {
                switch (row['__dt_flg']) {
                    case "Z":
                        var gidx = row['__dt_lvl'];
                        var lvstr = "";
                        for (var ll = 0; ll < gidx; ll++) {
                            lvstr += whitespace;
                        }
                        lvstr += whitespace;
                        Handsontable.Dom.fastInnerHTML(td, lvstr + (value || ''));
                        break;
                    case "G":
                        var gidx = row['__dt_lvl'];
                        var lvstr = "";
                        for (var ll = 0; ll < gidx; ll++) {
                            lvstr += whitespace;
                        }
                        lvstr += '<span class="groupsArrow"></span>  ';

                        var html = "<div style='position:absolute;'>";
                        html += lvstr + (value || '<span style="opacity:.5">...</span>');
                        html += "</div>"

                        Handsontable.Dom.fastInnerHTML(td, html);
                        break;
                }
            }
        }
        return td;
    }

    Handsontable.renderers.registerRenderer('groups', groupsRenderer);

})
        (Handsontable);
