<div ng-controller="FormBuilderLayoutController">
    <!-- form-builder-content -->
    <div class="form-builder-saving">
        <span ng-show='saving'>
            <i class="fa fa-refresh fa-spin"></i>
            Saving...
        </span>
        <span ng-show='!saving && layoutChanging'>
            <i class="fa fa-refresh fa-spin"></i>
            Rendering Layout...
        </span>
    </div>
    <script type="text/ng-template" id="index_builder_field"><?php include('form_builder_layout_field.php'); ?></script>
    <div id="render-builder">
        <div ui-content class="form-builder" style="top:27px;padding-top:17px;">
            <form class="form-horizontal" role="form" style="padding-bottom:500px;">
                <div oc-lazy-load="{name: 'ui.tree', files: ['<?= $this->staticUrl('/js/lib/angular.ui.tree.js') ?>']}">
                    <div class='field-tree' ui-tree="fieldsOptions" ng-init="treeName = 'formBuilder.fields'">
                        <ol ui-tree-nodes ng-model="$editor.activeTab.fields">
                            <li ng-repeat="field in $editor.activeTab.fields"  
                                ng-class="{
                                            inline:field.displayInline || field.display == 'inline'
                                        }" 
                                class="{{field.type}}"
                                ui-tree-node ng-include="'index_builder_field'"></li>
                        </ol>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /form-builder-content -->
</div>
<script type="text/javascript">
    /* globals app,$,editor, */
    app.controller("FormBuilderLayoutController", function ($scope, $http, $timeout, $window, $compile, $localStorage) {
        editor.formBuilder.builder = $scope;
        editor.formBuilder.$scope = $scope;
        editor.formBuilder.$http = $http;
        editor.formBuilder.$timeout = $timeout;
        editor.formBuilder.$window = $window;
        editor.formBuilder.$compile = $compile;
        editor.formBuilder.$localStorage = $localStorage;
        $scope.$editor = editor;
        $scope.editor = editor.formBuilder;
        $scope.form = editor.activeTab.form;


//        if (editor.activeTab.fields[editor.activeTab.fields.length - 1].type != "EOF-PLACEHOLDER") {
//            editor.activeTab.fields.push({
//                type: "EOF-PLACEHOLDER"
//            });
//        }

        $scope.$watch('$editor.activeTab.form', function (n, o) {
            $scope.form = $scope.$editor.activeTab.form;
        }, true);

        var selectTimeout = null;
        $scope.setActive = function (res) {
            editor.activeTab.active = $scope.active = res;
            $scope.editor.properties.setActive(res);
        }
        $scope.setActiveTree = function (res) {
            $scope.activeTree = res;
        }
        $scope.setLayout = function (res) {
            editor.activeTab.layout = $scope.layout = res;
        }
        $scope.select = function (item, event) {
            event.stopPropagation();
            event.preventDefault();
            $(".form-field.active").removeClass("active");
            $(event.currentTarget).addClass("active");
            $(".toolbar-type").removeClass('open');
            clearTimeout(selectTimeout);
            $scope.setActive(null);
            $scope.setActiveTree(null);
            $scope.propMsg = 'Loading Field';
            selectTimeout = setTimeout(function () {
                $scope.$apply(function () {
                    $scope.inEditor = true;
                    $scope.setActiveTree(item);
                    $scope.setActive(item.$modelValue);
                    if (!!editor.formBuilder.types[$scope.active.type]) {
                        $scope.editor.properties.activeEditor = editor.formBuilder.types[$scope.active.type];
                    } else {
                        $scope.editor.properties.activeEditor = null;
                    }
                    editor.activeTab.sidebar.properties = true;

                    switch (item.$modelValue.type) {
                        case 'DataFilter':
                        case 'DataGrid':
                        case 'DataTable':
                        case 'GridView':
                        case 'ListView':
                            $scope.editor.properties.getDataSourceList();
                            break;
                        case 'RelationField':
                        case 'TextField':
                            $scope.editor.properties.generateRelationField();
                            break;
                        default :
                            if (item.$modelValue.type.substr(0, 5).toLowerCase() == "chart") {
                                $scope.editor.properties.getDataSourceList();
                                $scope.editor.properties.setTickSeries();
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
        $scope.unselect = function () {
            $scope.setActive(null);
            $scope.activeEditor = null;
        };
        $scope.unselectViaJquery = function () {
            $scope.$apply(function () {
                $scope.setActive(null);
                $scope.setLayout(null);
            });
        };
        $scope.save = function () {
            alert("SAVED");
        }
        $scope.deleteField = function () {
            var $el = $($scope.activeTree.$parent.$element);
            var $old = $scope.activeTree;
            $timeout(function () {
                if ($el.next().length > 0 && !$el.next().hasClass('cpl')) {
                    $el.next().find(".form-field:eq(0)").click();
                } else if ($el.prev().length > 0 && !$el.prev().hasClass('cpl')) {
                    $el.prev().find(".form-field:eq(0)").click();
                } else {
                    $scope.unselect();
                }

                $old.remove();
                
                $timeout(function() {
                    $scope.detectDuplicate();
                });
            }, 0);
        }
        $scope.changeListViewMode = function () {
            if ($scope.active.fieldTemplate != 'datasource') {
                $scope.active.name = $scope.modelFieldList['DB Fields'][Object.keys($scope.modelFieldList['DB Fields'])[0]];
            }
        }
        $scope.changeActiveName = function () {
            var $el = $(":focus");
            if (typeof $el != "undefined") {
                var newName = $scope.formatName($scope.active.name);
                var caretPos = $el.getCaretPosition() - ($scope.active.name.length - newName.length);
                $el.val(newName).setCaretPosition(caretPos);
                $scope.active.name = newName;
            }
            $scope.detectDuplicate();
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
                    case "RelationField":
                        return name + field.identifier;
                    default:
                        return name;
                }
            }
        }
        $scope.detectDuplicate = function () {
            var names = {};
            $(".duplicate").each(function () {
                var fname = $(this).attr('fname');
                if (fname == '') {
                    return;
                }
                else {
                    names[fname] = (names[fname] + 1) || 1;
                }
            });
            for (var key in names) {
                var item = names[key];
                if (item > 1) {
                    $("[fname=" + key + "]").removeClass('ng-hide');
                } else {
                    $("[fname=" + key + "]").addClass('ng-hide');
                }
            }
        }
        $scope.fieldCount = 0;
        $scope.loadedFieldCount = 0;
        $scope.initField = function (field) {
            field.$isPlaceholder = $scope.isPlaceholder(field);
            $scope.fieldCount++;
        }
        $scope.increaseLoadedField = function () {
            $scope.loadedFieldCount++;
            if ($scope.fieldCount == $scope.loadedFieldCount) {
                if (editor.activeTab.initTimeout) {
                    clearTimeout(editor.activeTab.initTimeout);
                }

                editor.activeTab.initTimeout = setTimeout(function () {
                    editor.stopLoading(editor.activeTab.alias);
                }, 200);
            } else {
                editor.startLoading(editor.activeTab.alias);
            }
        }
        $scope.isPlaceholder = function (field) {
            if (field == editor.activeTab.fields[editor.activeTab.fields.length - 1]) {
                if (editor.formBuilder.finishRenderTimeout !== null) {
                    clearTimeout(editor.formBuilder.finishRenderTimeout);
                }
                editor.formBuilder.finishRenderTimeout = setTimeout(function () {
                    editor.formBuilder.types.ColumnField.refreshColumnPlaceholder();
                }, 400);
            }
            if ((field.type == 'Text' && field.value == '<column-placeholder></column-placeholder>') || field.type == null)
                return 'true';
        };
        $scope.detectEmptyPlaceholder = function () {
            $timeout(function () {
                $('.field-tree .angular-ui-tree-empty').remove();
                if (editor.activeTab.fields.length == 0) {
                    $('<div class="angular-ui-tree-empty"></div>').appendTo('.field-tree');
                }
            }, 10);
        }

        $scope.relayout = function (field) {
            if (!!field && !!editor && !!editor[field.type] && typeof editor[field.type].onLoad == 'function') {
                editor[field.type].onLoad(field);
            }
            $scope.detectDuplicate();
            $scope.increaseLoadedField();
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
                    // scope.elements.placeholder.replaceWith(scope.elements.dragging.clone().find('li:eq(0)'));
                }
            },
            dragStop: function (scope) {
                if ($scope.isCloning) {
                    $scope.isCloning = false;
                    $scope.isCloneDragging = false;
                    var field = angular.copy(scope.source.nodeScope.$modelValue);
                    scope.source.nodesScope.$modelValue.splice(scope.source.index, 0, field);
                }
                $timeout(function () {
                    editor.formBuilder.types.ColumnField.refreshColumnPlaceholder();
                }, 10);
            },
            accept: function(source, destNodesScope, destIndex) {
                if (source.treeName == 'formBuilder.fields') {
                    return true;
                }
                return false;
            }
        };

    });
</script>