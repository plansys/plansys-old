<script type = "text/javascript" >
    /** jQuery Caret **/
    (function ($) {
        // Behind the scenes method deals with browser
        // idiosyncrasies and such
        $.caretTo = function (el, index) {
            if (el.createTextRange) {
                var range = el.createTextRange();
                range.move("character", index);
                range.select();
            } else if (el.selectionStart != null) {
                el.focus();
                el.setSelectionRange(index, index);
            }
        };

        // Set caret to a particular index
        $.fn.setCaretPosition = function (index, offset) {
            return this.queue(function (next) {
                if (isNaN(index)) {
                    var i = $(this).val().indexOf(index);
                    if (offset === true) {
                        i += index.length;
                    } else if (offset) {
                        i += offset;
                    }

                    $.caretTo(this, i);
                } else {
                    $.caretTo(this, index);
                }

                next();
            });
        };
        $.fn.getCaretPosition = function () {
            var input = this.get(0);
            if (!input)
                return; // No (input) element found
            if ('selectionStart' in input) {
                // Standard-compliant browsers
                return input.selectionStart;
            } else if (document.selection) {
                // IE
                input.focus();
                var sel = document.selection.createRange();
                var selLen = document.selection.createRange().text.length;
                sel.moveStart('character', -input.value.length);
                return sel.text.length - selLen;
            }
        }
    })(jQuery);

var editor = {};

app.controller("PageController", function ($scope, $http, $timeout, $window, $compile, $localStorage) {
    editor.$scope = $scope;
    editor.$http = $http;
    editor.$timeout = $timeout;
    editor.$window = $window;
    editor.$compile = $compile;
    editor.$localStorage = $localStorage;
    $scope.editor = editor;
    $scope.activeEditor = null;


    window.$(document).keydown(function (event) {
        if (!( String.fromCharCode(event.which).toLowerCase() == 's' && (event.metaKey || event.ctrlKey)) && !(event.which == 19)) return true;
        $scope.save();
        event.preventDefault();
        return false;
    });

    $scope.getNumber = function (num) {
        a = [];
        for (i = 1; i <= num; i++) {
            a.push(i);
        }
        return a;
    };
    var vis = (function () {
        var stateKey,
            eventKey,
            keys = {
                hidden: "visibilitychange",
                webkitHidden: "webkitvisibilitychange",
                mozHidden: "mozvisibilitychange",
                msHidden: "msvisibilitychange"
            };
        for (stateKey in keys) {
            if (stateKey in document) {
                eventKey = keys[stateKey];
                break;
            }
        }
        return function (c) {
            if (c)
                document.addEventListener(eventKey, c);
            return !document[stateKey];
        }
    })();
    vis(function () {
        if (vis()) {
            $scope.save();
        }
    });
    $scope.inEditor = true;
    $scope.classPath = '<?= $classPath; ?>';
    $scope.fieldMatch = function (scope) {

        if (scope == null)
            return false;
        if ($scope.active == null)
            return false;
        if (scope.modelClass == null)
            return false;

        if (scope.modelClass.indexOf(".") >= 0) {
            scope.modelClass = scope.modelClass.split(".").pop();
        }


        if ($scope.active.type != scope.modelClass)
            return false;

        return true;
    }
    /*********************** TOOLBAR TABS ***********************/
    $scope.tabs = {
        toolbar: false,
        properties: true
    };
    /*********************** FORM PROPERTIES ***********************/
    $scope.createUrl = function (action) {
        var module = '<?php echo $fb->module; ?>';
        var controller = $scope.form.controller.replace('Controller', '');
        controller = controller.charAt(0).toLowerCase() + controller.slice(1);
        action = action.replace('action', '');
        action = action.charAt(0).toLowerCase() + action.slice(1);
        return Yii.app.createUrl(module + '/' + controller + '/' + action);
    };
    $scope.updateRenderBuilder = function () {
        var renderBuilderUrl = Yii.app.createUrl('dev/forms/renderBuilder', {
            class: '<?= $classPath ?>',
            layout: $scope.form.layout.name
        });
        $http.post(renderBuilderUrl, {form: $scope.form}).then(function (response) {
            $("#render-builder").html(response.data);
            $compile($("#render-builder").contents())($scope);
        });
    };
    $scope.mustReload = false;
    $scope.saveForm = function () {
        if ($scope.layout != null) {
            var name = $scope.layout.name;
            $scope.form.layout.data[name] = $scope.layout;
        }

        if ($scope.form.layout.name == 'full-width' || $scope.form.layout.name == 'dashboard') {
            $scope.form.layout.data.col1.size = "100";
        }

        $scope.saving = true;
        var url = '<?= $this->createUrl("save", ['class' => $classPath, 'timestamp' => $fb->timestamp]); ?>';
        $http.post(url, {form: $scope.form})
            .success(function (data, status) {
                $scope.saving = false;
                $scope.updateRenderBuilder();
                if (data == 'FAILED') {
                    $scope.mustReload = true;
                    $("#must-reload").show();
                    location.reload();
                } else if (data == 'FAILED: PERMISSION DENIED') {
                    alert('ERROR: Failed to write file\nReason: Permission Denied');
                }

            })
            .error(function (data, status) {
                $scope.saving = false;
            });
    };
    $scope.actionUrl = function (item) {
        if (typeof item != "undefined") {
            return Yii.app.createUrl(item);
        }
        return "";
    }
    $scope.generateCreateAction = function () {
        $scope.saving = true;
        $scope.createAction = true;
        $http.post('<?php echo $this->createUrl("createAct", array('class' => $class)); ?>', {form: $scope.form})
            .success(function (data, status) {
                $scope.saving = false;
            })
            .error(function (data, status) {
                $scope.saving = false;
            });
    };
    $scope.generateUpdateAction = function () {
        $scope.saving = true;
        $scope.updateAction = true;
        $http.post('<?php echo $this->createUrl("updateAct", array('class' => $class)); ?>', {form: $scope.form})
            .success(function (data, status) {
                $scope.saving = false;
            })
            .error(function (data, status) {
                $scope.saving = false;
            });
    };
    $scope.openToolbarType = function (open) {
        if (open) {
            $(".toolbar-type li.hover").removeClass('hover');
            $(".toolbar-type li a[value='" + $scope.active.type + "']").focus().parent().addClass('hover');
        }
    }

    $scope.filterFileName = function (model, field) {
        model[field] = model[field].replace(/[\\\/\:\*\?\'\<\>\|]/g, '');
    }

    /*********************** TEXT ********************************/
    $scope.aceLoaded = function (_editor) {
        $(window).resize();
    };
    $(window).resize(function () {
        $(".text-editor").height($(".form-builder-properties [ui-content]").height() - 45);
    });
    /*********************** LAYOUT ********************************/
    $scope.layout = null;
    $scope.selectLayout = function (layout, func) {
        $scope.tabs.properties = true;
        $scope.active = null;
        $(".form-field.active").removeClass("active");
        if ($scope.form.layout.data[layout] == null) {
            $scope.form.layout.data[layout] = {
                type: '',
            }
        }

        if ($scope.form.layout.name == "full-width" || $scope.form.layout.name == "dashboard") {
            return true;
        }

        $scope.typeChanging = true;
        $scope.layout = null;
        $timeout(function () {
            $scope.typeChanging = false;
            $scope.layout = angular.extend({}, $scope.form.layout.data[layout]);
            $scope.layout.name = layout;
            if ($scope.layout.sizetype == null) {
                $scope.layout.sizetype = "%";
            }

            if (typeof func == "function") {
                func();
            }
        }, 0);
    }
    $scope.unselectLayout = function () {
        $scope.active = null;
        $scope.layout = null;
    };
    $scope.changeLayoutType = function () {
        $scope.form.layout.data = {};
        function setsize(size, sizetype, type) {
            var val = {};
            val.size = size;
            val.sizetype = sizetype;
            val.type = (type ? type : "");
            return val;
        }

        switch ($scope.form.layout.name) {
            case "full-width":
            case "dashboard":
                location.reload();
                $scope.form.layout.data.col1 = setsize("", "", "mainform");
                break;
            case "2-cols":
                $scope.form.layout.data.col1 = setsize("200", "px");
                $scope.form.layout.data.col2 = setsize("", "", "mainform");
                break;
            case "3-cols":
                $scope.form.layout.data.col1 = setsize("200", "px");
                $scope.form.layout.data.col2 = setsize("", "", "mainform");
                $scope.form.layout.data.col3 = setsize("", "");
                break;
            case "2-rows":
                $scope.form.layout.data.row1 = setsize("", "", "mainform");
                $scope.form.layout.data.row2 = setsize("", "");
                break;
        }

        $scope.saveForm(true);
    };
    $scope.changeMenuTreeFile = function () {
        var file = this.value;
        $http.get(Yii.app.createUrl('/dev/genMenu/getOptions', {
            path: file
        })).success(function (data) {
            $scope.selectLayout($scope.layout.name, function () {
                for (i in $scope.layout) {
                    if (["type", "name", "file"].indexOf(i) >= 0)
                        continue;
                    if (!!data.layout && !!data.layout[i]) {
                        $scope.layout[i] = data.layout[i];
                    } else {
                        switch (i) {
                            case 'size':
                                $scope.layout[i] = '200';
                                break;
                            case 'sizetype':
                                $scope.layout[i] = 'px';
                                break;
                            default:
                                $scope.layout[i] = '';
                        }
                    }
                }
                $scope.layout.file = file;
                $scope.changeLayoutProperties();
            });
        }).error(function () {
            alert("This menu can not be read (PHP Error)");
        });
    }
    $scope.changeLayoutProperties = function () {
        $scope.saveForm();
    };
    $scope.changeLayoutSectionType = function () {
        if ($scope.layout.type == "mainform") {
            for (v in $scope.form.layout.data) {
                if ($scope.layout.name != v && $scope.form.layout.data[v].type == "mainform") {
                    $scope.form.layout.data[v].type = '';
                }
            }
        }

        $scope.saveForm();
    };
    /*********************** FIELDS ********************************/
    $scope.modelFieldList = <?php echo json_encode(FormsController::$modelFieldList); ?>;
    $scope.relFieldList = <?php echo json_encode(FormsController::$relFieldList); ?>;
    $scope.dataSourceList = {};
    $scope.toolbarSettings = <?php echo json_encode(FormField::settings($formType)); ?>;
    $scope.form = <?php echo json_encode($fb->form); ?>;
    /*********************** FIELD LOCAL STORAGE SYNC *****************/
    $scope.$storage = $localStorage;
    $scope.storageWatch = null;
    $scope.serverFields = <?php echo json_encode($fieldData); ?>;
    if ($scope.form.layout.name == "dashboard") {
        if (!$scope.$storage.plansysFormBuilder) {
            $scope.$storage.plansysFormBuilder = {};
        }
        if (!$scope.$storage.plansysFormBuilder[$scope.classPath]) {
            $scope.$storage.plansysFormBuilder[$scope.classPath] = [];
        }
        $scope.$storage.plansysFormBuilder[$scope.classPath] = $scope.serverFields;
        $scope.fields = $scope.$storage.plansysFormBuilder[$scope.classPath];
        $scope.storageWatch = $scope.$watch('$storage', function (n, o) {
            if ($scope.$storage.plansysFormBuilder[$scope.classPath] != $scope.fields) {
                $scope.fields = $scope.$storage.plansysFormBuilder[$scope.classPath];
                $scope.unselect();
            }
        }, true);
    } else {
        $scope.fields = $scope.serverFields;
    }

    $scope.saving = false;
    /************************ RELATION FIELD  ****************************/
    $scope.relationFieldList = {};
    $scope.generateRelationField = function (modelClass, parentScope) {
        modelClass = modelClass || $scope.active.modelClass;
        $http.get(Yii.app.createUrl('/formfield/RelationField.listField', {
            class: modelClass
        })).success(function (data) {
            $scope.relationFieldList = data;
            if (parentScope != null && typeof parentScope.updateListView != "undefined") {
                parentScope.updateListView();
            }
        });
    }
    /************************ TEXT AUTO COMPLETE  ****************************/
    $scope.generateAutoComplete = function () {
        switch ($scope.active.autocomplete) {
            case "rel":
                $timeout(function () {
                    $scope.active.modelClass = '<?= Helper::classAlias(get_parent_class($class), false) ?>';
                    $scope.active.idField = $scope.active.name;
                    $scope.active.labelField = $scope.active.name;
                    $scope.active.criteria = {
                        'select': $scope.active.name,
                        'distinct': 'true',
                        'alias': 't',
                        'condition': '{[search]}',
                        'order': '',
                        'group': '',
                        'having': '',
                        'join': ''
                    };
                    $scope.save();
                });
                break;
        }
    }

    /************************ DATA CHART SERIES ****************************/
    $scope.generateSeries = function (retrieveMode) {
        var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
        if (confirm("Your current series will be lost. Are you sure?")) {
            $scope.active.series = [];
            $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                name: $scope.active.datasource,
                class: '<?= Helper::classAlias($class) ?>',
                generate: 1
            }).success(function (data) {
                if (typeof data == 'object') {
                    if (typeof data.data == 'object') {
                        data = data.data;
                    } else {
                        return;
                    }

                    var generated;
                    switch (retrieveMode) {
                        case 'by Row' :
                            generated = generateByRow(data);
                            break;
                        case 'by Column' :
                            generated = generateByColumn(data);
                            break;
                    }
                    if (typeof generated == "object" && generated.length > 0) {
                        $scope.active.series = generated[0];
                        $scope.setTickSeries();
                    } else {
                        alert("Field generation failed");
                    }
                    /*****  FUNCTION *****/

                    function generateByRow(data) {
                        var filtered = [];
                        for (var i in data) {
                            var rowcontent = {};
                            for (var j in data[i]) {
                                rowcontent[j] = data[i][j];
                            }
                            filtered.push(rowcontent);
                        }

                        var result = [];
                        for (var i in filtered) {
                            if (typeof result[i] == "undefined") {
                                result[i] = [];
                            }

                            for (var j in filtered[i]) {
                                var series = angular.extend({}, templateAttr);
                                series.value = filtered[i][j];
                                series.label = j;
                                series.color = getRandomColor();
                                result[i].push(series);
                            }
                        }

                        return result;
                    }

                    function generateByColumn(data) {
                        var filtered = {};
                        for (var i in data) {
                            for (var j in data[i]) {
                                if (typeof filtered[j] == "undefined") {
                                    filtered[j] = [];
                                }
                                filtered[j].push(data[i][j]);
                            }
                        }

                        var color;
                        var result = [];
                        result[0] = [];
                        for (var i in filtered) {
                            var series = angular.extend({}, templateAttr);
                            series.label = i;
                            series.value = filtered[i];
                            series.color = getRandomColor();
                            result[0].push(series);
                        }

                        return result;
                    }

                    function getRandomColor() {
                        var letters = '0123456789ABCDEF'.split('');
                        var color = '#';
                        for (var i = 0; i < 6; i++) {
                            color += letters[Math.floor(Math.random() * 16)];
                        }
                        return color;
                    }

                    $scope.save();
                }

            });
        }
    }

    $scope.setTickSeries = function () {
        var series = $scope.active.series;
        $scope.tickSeriesList = {
            '': '-- NONE --',
            '---': '---'
        };
        for (var i in series) {
            $scope.tickSeriesList[series[i].label] = series[i].label;
        }
    }

    function toTitleCase(str) {
        // Replace special characters with a space
        str = str.replace(/[^a-zA-Z0-9 ]/g, " ");
        // put a space before an uppercase letter
        str = str.replace(/([a-z](?=[A-Z]))/g, '$1 ');
        // Lower case first character and some other stuff that I don't understand
        str = str.replace(/([^a-zA-Z0-9 ])|^[0-9]+/g, '').trim().toLowerCase();
        // uppercase characters preceded by a space or number
        str = str.replace(/([ 0-9]+)([a-zA-Z])/g, function (a, b, c) {
            return b.trim() + ' ' + c.toUpperCase();
        });
        return str[0].toUpperCase() + str.substr(1);
    }

    /************************ DATA FILTERS ****************************/
    $scope.generateFilters = function () {
        var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
        if (confirm("Your current filters will be lost. Are you sure?")) {
            $scope.active.filters = [];
            $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                name: $scope.active.datasource,
                class: '<?= Helper::classAlias($class) ?>',
                generate: 1
            }).success(function (data) {
                if (typeof data == 'object') {
                    if (typeof data.data == 'object') {
                        data = data.data;
                    } else {
                        return;
                    }

                    if (data != null && data.length > 0 && typeof data[0] == "object") {
                        for (i in data[0]) {
                            var filter = angular.extend({}, templateAttr);
                            filter.name = i;
                            filter.label = toTitleCase(i);
                            if (i == 'id') {
                                filter.filterType = 'number';
                            }

                            if (typeof data[0][i] == "string" && data[0][i].match(/\d\d\d\d-(\d)?\d-(\d)?\d(.*)/g)) {
                                filter.filterType = 'date';
                            }

                            $scope.active.filters.push(filter);
                        }
                        $scope.save();
                    } else {
                        alert("WARNING: Filter Generator failed!\n\nYour query result is empty.\nPlease make sure your result returned more than one row.\n\n");
                    }
                }

            });
        }
    }

    /************************ DATA COLUMNS ****************************/
    $scope.dsGroupCols = {};
    $scope.getDSGroupCols = function () {
        var name = $scope.active.name;
        if (!!$scope.active.datasource) {
            name = $scope.active.datasource;
        }
        if (!!name) {
            $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                name: name,
                class: '<?= Helper::classAlias($class) ?>',
                generate: 1
            }).success(function (data) {
                if (typeof data == 'object') {
                    if (typeof data.data == 'object') {
                        data = data.data;
                    } else {
                        return;
                    }

                    $scope.dsGroupCols = {};
                    if (!!$scope.active.datasource) {
                        $scope.dsGroupCols = {'': 'Current Column', '---': '---'};
                    }
                    if (data != null && data.length > 0 && typeof data[0] == "object") {
                        for (i in data[0]) {
                            $scope.dsGroupCols[i] = i;
                        }
                    }
                }
            });
        }
    }
    $scope.generateColumns = function () {
        var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
        if (confirm("Your current columns will be lost. Are you sure?")) {
            $scope.active.columns.splice(0, $scope.active.columns.length);
            $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                name: $scope.active.datasource,
                class: '<?= Helper::classAlias($class) ?>',
                generate: 1
            }).success(function (data) {
                if (typeof data == 'object') {
                    if (typeof data.data == 'object') {
                        data = data.data;
                    } else {
                        return;
                    }

                    if (data != null && data.length > 0 && typeof data[0] == "object") {
                        for (i in data[0]) {
                            var filter = angular.extend({}, templateAttr);
                            for (k in filter) {
                                if (['columnType', 'name', 'label', 'show'].indexOf(k) < 0
                                    && templateAttr.typeOptions[filter.columnType].indexOf(k) < 0) {
                                    delete filter[k];
                                }
                            }

                            filter.name = i;
                            filter.label = toTitleCase(i);
                            $scope.active.columns.push(filter);
                        }
                        $scope.save();
                    } else {
                        alert("WARNING: Column Generator failed!\n\nYour query result is empty.\nPlease make sure your query has returned any row!\n\n");
                    }
                }
            });
        }
    }

    $scope.isPlaceholder = function (field) {
        if ((field.type == 'Text' && field.value == '<column-placeholder></column-placeholder>') || field.type == null)
            return true;
    };
    $scope.getDataSourceList = function () {
        var dslist = {
            '': '-- EMPTY --',
            '---': '---'
        };

        function recurseFields(f) {
            for (i in f) {
                var x = f[i];
                if (typeof f[i] != 'object')
                    continue;

                if (f[i].type == 'DataSource') {
                    dslist[f[i].name] = f[i].name;
                }

                for (k in f[i].parseField) {
                    recurseFields(x[k]);
                }
            }
        }

        recurseFields($scope.fields);

        $scope.dataSourceList = dslist;
    }

    $scope.changeListViewMode = function () {
        if ($scope.active.fieldTemplate != 'datasource') {
            $scope.active.name = $scope.modelFieldList['DB Fields'][Object.keys($scope.modelFieldList['DB Fields'])[0]];
        }
        $scope.save();
    }
    $scope.changeActiveName = function () {
        $el = $(":focus");
        if (typeof $el != "undefined") {
            var newName = $scope.formatName($scope.active.name);
            var caretPos = $el.getCaretPosition() - ($scope.active.name.length - newName.length);
            $el.val(newName).setCaretPosition(caretPos);
            $scope.active.name = newName;
        }
        $scope.detectDuplicate();
        $scope.save();
    }
    $scope.formatName = function (name) {
        if (typeof name != "undefined" && name != null) {
            return name.replace(/[^a-z0-9A-Z_]/gi, '');
        } else {
            return "";
        }
    }
    $scope.generateIdentity = function (field) {
        if (!!field.name) {
            var name = $scope.formatName(field.name);
            switch (field.type) {
                case "SectionHeader":
                case "Text":
                case "ColumnField":
                case "SubForm":
                case "ModalDialog":
                    return "";
                    break;
                case "RelationField":
                    return name + field.identifier;
                    break;
                default:
                    return name;
                    break;
            }
        }
    }
    $scope.detectDuplicate = function () {
        $(".duplicate").addClass('ng-hide').each(function () {
            if ($(this).attr('fname') == '')
                return;

            var name = ".d-" + $scope.formatName($(this).attr('fname'));
            var $name = $(name);
            if (name.trim() != ".d-") {
                if ($name.length > 1) {
                    $(this).removeClass('ng-hide');
                }
            }
        });
    }
    $scope.detectEmptyPlaceholder = function () {
        $timeout(function () {
            $('.field-tree .angular-ui-tree-empty').remove();
            if ($scope.fields.length == 0) {
                $('<div class="angular-ui-tree-empty"></div>').appendTo('.field-tree');
            }
        }, 10);
    }
    $scope.relayout = function (field) {
        if (!!field && !!editor && !!editor[field.type] && typeof editor[field.type].onLoad == 'function') {
            editor[field.type].onLoad(field);
        }
        $scope.detectDuplicate();
    }
    $scope.isCloning = false;
    $scope.isCloneDragging = false;
    $scope.prepareCloneField = function (scope) {
        $scope.isCloning = true;
    }
    $scope.cancelCloneField = function () {
        $timeout(function () {
            if (!$scope.isCloneDragging) {
                $scope.isCloning = false;
            }
        }, 10);
    }
    $scope.fieldsOptions = {
        dragStart: function (scope) {
            if ($scope.isCloning) {
                $scope.isCloneDragging = true;
                scope.elements.placeholder.replaceWith(scope.elements.dragging.clone().find('li:eq(0)'));
            }
        },
        dragStop: function (scope) {
            if ($scope.isCloning) {
                $scope.isCloning = false;
                $scope.isCloneDragging = false;
                var field = angular.copy(scope.source.nodeScope.$modelValue);
                scope.source.nodesScope.$modelValue.splice(scope.source.index, 0, field);
            }
            $scope.save();
            $timeout(function () {
                editor.ColumnField.refreshColumnPlaceholder();
            }, 10);
        }
    };
    $scope.active = null;
    $scope.activeTree = null;
    $scope.deleteField = function () {
        $el = $($scope.activeTree.$parent.$element);
        $old = $scope.activeTree;
        $timeout(function () {
            if ($el.next().length > 0 && !$el.next().hasClass('cpl')) {
                $el.next().find(".form-field:eq(0)").click();
            } else if ($el.prev().length > 0 && !$el.prev().hasClass('cpl')) {
                $el.prev().find(".form-field:eq(0)").click();
            } else {
                $scope.unselect();
            }

            $old.remove();
            $scope.save();
        }, 0);
    }
    $scope.save = function () {
        if (!$scope.saving) {
            $scope.detectEmptyPlaceholder();
            $scope.saving = true;
            var url = '<?= $this->createUrl("save", ['class' => $classPath, 'timestamp' => $fb->timestamp]); ?>';
            $http.post(url, {fields: $scope.fields})
                .success(function (data, status) {
                    $scope.saving = false;
                    $scope.detectDuplicate();
                    if (data == 'FAILED') {
                        $scope.mustReload = true;
                        $("#must-reload").show();
                        location.reload();
                    } else if (data == 'FAILED: PERMISSION DENIED') {
                        alert('ERROR: Failed to write file\nReason: Permission Denied');
                    }
                })
                .error(function (data, status) {
                    $scope.saving = false;
                });
        }
    };
    var selectTimeout = null;
    $scope.propMsg = 'Welcome To Form Builder';
    $scope.select = function (item, event) {
        event.stopPropagation();
        event.preventDefault();
        $(".form-field.active").removeClass("active");
        $(event.currentTarget).addClass("active");
        $(".toolbar-type").removeClass('open');
        clearTimeout(selectTimeout);
        $scope.active = null;
        $scope.activeTree = null;
        $scope.propMsg = 'Loading Field';
        selectTimeout = setTimeout(function () {
            $scope.$apply(function () {
                if ($scope.active == null || item.$modelValue.type != $scope.active.type) {
                    $("#toolbar-properties").hide();
                }
                $scope.inEditor = true;
                $scope.activeTree = item;
                $scope.active = item.$modelValue;
                if (!!editor[$scope.active.type]) {
                    $scope.activeEditor = editor[$scope.active.type];
                } else {
                    $scope.activeEditor = null;
                }
                $scope.tabs.properties = true;
                switch (item.$modelValue.type) {
                    case 'DataFilter':
                    case 'DataGrid':
                    case 'DataTable':
                    case 'GridView':
                    case 'ListView':
                        $scope.getDataSourceList();
                        break;
                    case 'RelationField':
                    case 'TextField':
                        $scope.generateRelationField();
                        break;
                    default :
                        if (item.$modelValue.type.substr(0, 5).toLowerCase() == "chart") {
                            $scope.getDataSourceList();
                            $scope.setTickSeries();
                        }
                        break;
                }

                if ($scope.activeEditor != null && typeof $scope.activeEditor.onSelect == 'function') {
                    $scope.activeEditor.onSelect($scope.active);
                }

                $scope.propMsg = 'Welcome To Form Builder';
            });
        }, 10);
    };
    $scope.selected = function () {
        $("#toolbar-properties").show();
    }
    $scope.unselect = function () {
        $scope.active = null;
        $scope.activeEditor = null;
    };
    $scope.unselectViaJquery = function () {
        $scope.$apply(function () {
            $scope.active = null;
            $scope.layout = null;
        });
    };
    $scope.moveToPrev = function (scope) {
        var index = scope.$parent.index();
        var clone = scope.$parent.$parentNodesScope.$modelValue[index];
        var count = scope.$parent.$parentNodesScope.$modelValue.length;
        if (index - 1 >= 0) {
            scope.$parent.$parentNodesScope.$modelValue[index] = scope.$parent.$parentNodesScope.$modelValue[index - 1];
            scope.$parent.$parentNodesScope.$modelValue[index - 1] = clone;
        }
        $scope.save();
    }
    $scope.moveToNext = function (scope) {
        var index = scope.$parent.index();
        var clone = scope.$parent.$parentNodesScope.$modelValue[index];
        var count = scope.$parent.$parentNodesScope.$modelValue.length;
        if (index + 1 <= count - 1) {
            scope.$parent.$parentNodesScope.$modelValue[index] = scope.$parent.$parentNodesScope.$modelValue[index + 1];
            scope.$parent.$parentNodesScope.$modelValue[index + 1] = clone;
        }
        $scope.save();
    }
    $timeout(function () {
        $(document).trigger('formBuilderInit');
        $scope.detectEmptyPlaceholder();
        $scope.updateRenderBuilder();
    }, 100);
    $('body').on('click', 'div[ui-header]', function (e) {
        if (e.target == this) {
            $scope.unselectViaJquery();
        }
    });
    $('body').on('keydown', function (e) {
        if ($scope.active != null) {
            if ($(':focus').parents('.form-builder-properties').length > 0) {
                return
            }
            $el = $($scope.activeTree.$parent.$element);
            if ($("body > .modal-container").length == 0) {
                switch (e.which) {
                    case 9:
                        $("#toolbar-properties input, #toolbar-properties button").eq(0).focus();
                        e.preventDefault();
                        e.stopPropagation();
                        break;
                    case 38:
                        if ($el.prev().length > 0) {
                            $el.prev().find(".form-field:eq(0)").click();
                        }
                        break;
                    case 40:
                        if ($el.next().length > 0) {
                            $el.next().find(".form-field:eq(0)").click();
                        }
                        break;
                }
            }
        }


    });
    $('body').on('mouseenter', '.form-field', function (e) {
        var formfields = $(this).parentsUntil('div[ui-tree]', '.form-field');

        function setpos(el, i) {
            $(el).find('.field-info:eq(0)').css({
                'margin-top': (i * -1 * 20) + 'px',
            });
        }

        if (formfields.length > 0) {
            $(formfields).each(function (i) {
                setpos(this, i + 1);
            });
        } else {
            setpos(this, 0);
        }

        e.stopPropagation();
    });
});
</script >
