<script type="text/javascript">
    app.controller("PageController", function ($scope, $http, $timeout, $window, $compile) {
        $scope.getNumber = function (num) {
            a = [];
            for (i = 1; i <= num; i++) {
                a.push(i);
            }
            return a;
        };
        $scope.inEditor = true;
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
        $scope.dataSourceList = {};
        $scope.toolbarSettings = <?php echo json_encode(FormField::settings($formType)); ?>;
        $scope.form = <?php echo json_encode($fb->form); ?>;
        $scope.fields = <?php echo json_encode($fieldData); ?>;
        $scope.saving = false;

        /************************ RELATION FIELD  ****************************/
        $scope.relationFieldList = {};
        $scope.generateRelationField = function () {
            $http.get(Yii.app.createUrl('/formfield/RelationField.listField', {
                class: $scope.active.modelClass
            })).success(function (data) {
                $scope.relationFieldList = data;
            });
        }


        /************************ DATA FILTERS ****************************/
        $scope.generateFilters = function () {
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]").text());
            if (confirm("Your current filters will be lost. Are you sure?")) {
                $scope.active.filters = [];
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: $scope.active.datasource,
                    class: '<?= Helper::classAlias($class) ?>'
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
            var templateAttr = JSON.parse($("#toolbar-properties div[list-view] data[name=template_attr]").text());
            if (confirm("Your current filters will be lost. Are you sure?")) {
                $scope.active.columns = [];
                $http.post(Yii.app.createUrl('/formfield/DataSource.query'), {
                    name: $scope.active.datasource,
                    class: '<?= Helper::classAlias($class) ?>'
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
            var newName = $scope.formatName($scope.active.name);
            var caretPos = $el.getCaretPosition() - ($scope.active.name.length - newName.length);
            $el.val(newName).setCaretPosition(caretPos);
            $scope.active.name = newName;

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
        $scope.select = function (item, event) {
            event.stopPropagation();
            event.preventDefault();

            $(".form-field.active").removeClass("active");
            $(event.currentTarget).addClass("active");
            $(".toolbar-type").removeClass('open');

            clearTimeout(selectTimeout);
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
                            $scope.getDataSourceList();
                            break;
                        case 'DataGrid':
                            $scope.getDataSourceList();
                            break;
                        case 'RelationField':
                            $scope.generateRelationField();
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
                switch (e.which) {
                    case 9:
                        $("#toolbar-properties input, #toolbar-properties button").eq(0).focus();
                        e.preventDefault();
                        e.stopPropagation();
                        break;
                    case 46:
                        $scope.deleteField();
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
