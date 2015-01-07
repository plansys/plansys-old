<script type="text/javascript">

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
    app.controller("PageController", function ($scope, $http, $timeout, $window, $compile) {
        $scope.getNumber = function (num) {
            a = [];
            for (i = 1; i <= num; i++) {
                a.push(i);
            }
            return a;
        };
        $scope.inEditor = true;
        $scope.classPath = '<?= $classPath; ?>';
        $scope.fieldMatch = function (scope) {
            if (scope == null)
                return false;
            if ($scope.active == null)
                return false;
            if (scope.modelClass == null)
                return false;
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
        $scope.saveForm = function (force_reload) {
            if ($scope.layout != null) {
                var name = $scope.layout.name;
                $scope.form.layout.data[name] = $scope.layout;
            }

            if ($scope.form.layout.name == 'full-width') {
                $scope.form.layout.data.col1.size = "100";
            }

            $scope.saving = true;
            $http.post('<?php echo $this->createUrl("save", array('class' => $classPath)); ?>', {form: $scope.form})
                    .success(function (data, status) {
                        $scope.saving = false;
                        $scope.updateRenderBuilder();
                        if (data == 'FAILED') {
                            $scope.mustReload = true;
                            location.reload();
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
        $scope.selectLayout = function (layout) {
            $scope.tabs.properties = true;
            $scope.active = null;
            $(".form-field.active").removeClass("active");
            if ($scope.form.layout.data[layout] == null) {
                $scope.form.layout.data[layout] = {
                    type: '',
                }
            }

            if ($scope.form.layout.name == "full-width") {
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
        $scope.fields = <?php echo json_encode($fieldData); ?>;
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
                        switch (retrieveMode)
                        {
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
                                filter.label = i;
                                if (i == 'id') {
                                    filter.filterType = 'number';
                                }

                                if (typeof data[0][i] == "string" && data[0][i].match(/\d\d\d\d-(\d)?\d-(\d)?\d(.*)/g)) {
                                    filter.filterType = 'date';
                                }

                                $scope.active.filters.push(filter);
                            }
                            $scope.save();
                        }
                    }

                });
            }
        }

        /************************ DATA COLUMNS ****************************/
        $scope.generateColumns = function () {
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]:eq(0)").text());
            if (confirm("Your current columns will be lost. Are you sure?")) {
                $scope.active.columns = [];
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
                                filter.label = i;
                                $scope.active.columns.push(filter);
                            }
                            $scope.save();
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
            for (i in $scope.fields) {
                if ($scope.fields[i].type == 'DataSource') {
                    length++;
                    dslist[$scope.fields[i].name] = $scope.fields[i].name;
                }
            }

            $scope.dataSourceList = dslist;
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
            switch (field.type) {
                case "RelationField":
                    return  field.label;
                    break;
                default:
                    return field.name;
                    break;
            }

        }

        $scope.detectDuplicate = function () {
            $(".duplicate").addClass('ng-hide').each(function () {
                if ($(this).attr('fname') == '')
                    return;
                var name = ".d-" + $scope.formatName($(this).attr('fname'));
                var $name = $(name);
                if (name.trim() != ".d-" && name.indexOf("text/") != 0) {
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
        $scope.refreshColumnPlaceholder = function () {
            $(".cpl").each(function () {
                if ($(this).parent().find("li").length == 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        };
        $scope.relayout = function (type) {
            if (type == "ColumnField") {
                $scope.refreshColumnPlaceholder();
            }
            $scope.detectDuplicate();
        }
        $scope.isCloning = false;
        $scope.isCloneDragging = false;
        $scope.prepareCloneField = function (scope) {
            $scope.isCloning = true;
        }
        $scope.minimized = false;
        $scope.minimize = function () {
            $scope.minimized = true;
            var l = $(".fb1").parent().width() - 30;
            $(".fb1").width(l);
            $(".fb2").width(30).css('left', l);
        }
        $scope.maximize = function () {
            $scope.minimized = false;
            $(".fb1").width("69%");
            $(".fb2").width("31%").css("left", "69%");
        }
        $scope.cancelCloneField = function () {
            $timeout(function () {
                if (!$scope.isCloneDragging) {
                    $scope.isCloning = false;
                }
            }, 10);
        }
        $scope.fieldsOptions = {
            accept: function (s, d, i) {
                if (s.$modelValue.type == 'Portlet') {
                    var pl = $(".form-builder .angular-ui-tree-placeholder");
                    var width = "width: " + s.$modelValue.width + "px !important;";
                    var height = "height: " + s.$modelValue.height + "px;";

                    pl.addClass('Portlet');
                    pl.css('cssText', height + width);

                    if (d.$nodeScope != null && d.$nodeScope.$modelValue.type == 'Portlet') {
                        return false;
                    }
                }

                return true;
            },
            dragInit: function (e) {
                if (e.element.hasClass('Portlet')) {
                    e.placeholder.addClass('Portlet');
                    var height = "height: " + e.element.find('.portlet-container:eq(0)').height() + "px;";
                    var width = "width: " + e.element.find('.portlet-container:eq(0)').width() + "px !important;";
                    e.placeholder.css('cssText', height + width);

//                    e.pos.offsetX = e.element.offset().left;
//                    e.pos.offsetY = e.element.offset().top;
                }
            },
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
                    $scope.refreshColumnPlaceholder();
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
            $scope.detectEmptyPlaceholder();
            $scope.saving = true;
            $http.post('<?= $this->createUrl("save", array('class' => $classPath)); ?>', {fields: $scope.fields})
                    .success(function (data, status) {
                        $scope.saving = false;
                        $scope.detectDuplicate();
                        if (data == 'FAILED') {
                            $scope.mustReload = true;
                            location.reload();
                        }
                    })
                    .error(function (data, status) {
                        $scope.saving = false;
                    });
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
            $scope.propMsg = 'Loading Field'
            selectTimeout = setTimeout(function () {
                $scope.$apply(function () {
                    if ($scope.active == null || item.$modelValue.type != $scope.active.type) {
                        $("#toolbar-properties").hide();
                    }
                    $scope.inEditor = true;
                    $scope.activeTree = item;
                    $scope.active = item.$modelValue;
                    $scope.tabs.properties = true;
                    switch (item.$modelValue.type) {
                        case 'DataFilter':
                        case 'DataGrid':
                        case 'DataTable':
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

                });
            }, 10);
        };
        $scope.selected = function () {
            $("#toolbar-properties").show();
        }
        $scope.unselect = function () {
            $scope.active = null;
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
</script>
