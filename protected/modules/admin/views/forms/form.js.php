<script type="text/javascript">
    app.controller("PageController", function($scope, $http, $timeout, $window) {
        $scope.getNumber = function(num) {
            a = [];
            for (i = 1; i <= num; i++) {
                a.push(i);
            }
            return a;
        };
        $scope.inEditor = true;
        $scope.fieldMatch = function(scope) {
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
        $scope.createUrl = function(action) {
            var module = '<?php echo $fb->module; ?>';
            var controller = $scope.form.controller.replace('Controller', '');
            controller = controller.charAt(0).toLowerCase() + controller.slice(1);
            action = action.replace('action', '');
            action = action.charAt(0).toLowerCase() + action.slice(1);
            return Yii.app.createUrl(module + '/' + controller + '/' + action);
        };
        $scope.layoutChanging = false;
        $scope.cacheBuster = 1;

        $scope.saveForm = function(force_reload) {
            if ($scope.layout != null) {
                var name = $scope.layout.name;
                $scope.form.layout.data[name] = $scope.layout;
            }

            if ($scope.form.layout.name == 'full-width') {
                $scope.form.layout.data.col1.size = "100";
            }

            $scope.saving = true;
            $http.post('<?php echo $this->createUrl("save", array('class' => $class)); ?>', {form: $scope.form})
                    .success(function(data, status) {

                        if (force_reload === true) {

                            $window.location.reload();
                            return true;
                        }

                        $scope.saving = false;

                        if ($scope.layoutChanging) {
                            $scope.cacheBuster++;
                            $scope.layoutChanging = false;
                        }
                    })
                    .error(function(data, status) {
                        $scope.saving = false;
                    });
        };
        $scope.actionUrl = function(item) {
            if (typeof item != "undefined") {
                return Yii.app.createUrl(item);
            }
            return "";
        }
        $scope.generateCreateAction = function() {
            $scope.saving = true;
            $scope.createAction = true;
            $http.post('<?php echo $this->createUrl("createAct", array('class' => $class)); ?>', {form: $scope.form})
                    .success(function(data, status) {
                        $scope.saving = false;
                    })
                    .error(function(data, status) {
                        $scope.saving = false;
                    });
        };
        $scope.generateUpdateAction = function() {
            $scope.saving = true;
            $scope.updateAction = true;
            $http.post('<?php echo $this->createUrl("updateAct", array('class' => $class)); ?>', {form: $scope.form})
                    .success(function(data, status) {
                        $scope.saving = false;
                    })
                    .error(function(data, status) {
                        $scope.saving = false;
                    });
        };
        $scope.openToolbarType = function(open) {
            if (open) {
                $(".toolbar-type li a[value='" + $scope.active.type + "']").focus();
            }
        }
        /*********************** LAYOUT ********************************/
        $scope.layout = null;
        $scope.selectLayout = function(layout) {
            $scope.tabs.properties = true;
            $scope.active = null;
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
            $timeout(function() {
                $scope.typeChanging = false;
                $scope.layout = angular.extend({}, $scope.form.layout.data[layout]);
                $scope.layout.name = layout;
                if ($scope.layout.sizetype == null) {
                    $scope.layout.sizetype = "%";
                }
            }, 0);
        }
        $scope.unselectLayout = function() {
            $scope.active = null;
            $scope.layout = null;
        };
        $scope.changeLayoutType = function() {
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
        $scope.changeLayoutProperties = function() {
            $scope.layoutChanging = true;
            $scope.saveForm();
        };
        $scope.changeLayoutSectionType = function() {
            if ($scope.layout.type == "mainform") {
                for (v in $scope.form.layout.data) {
                    if ($scope.layout.name != v && $scope.form.layout.data[v].type == "mainform") {
                        $scope.form.layout.data[v].type = '';
                    }
                }
            }

            $scope.layoutChanging = true;
            $scope.saveForm();
        };
        /*********************** FIELDS ********************************/
        $scope.modelFieldList = <?php echo json_encode(FormsController::$modelFieldList); ?>;
        $scope.toolbarSettings = <?php echo json_encode(FormField::settings($formType)); ?>;
        $scope.form = <?php echo json_encode($fb->form); ?>;
        $scope.fields = <?php echo json_encode($fieldData); ?>;
        $scope.saving = false;

        $scope.isPlaceholder = function(field) {
            if ((field.type == 'Text' && field.value == '<column-placeholder></column-placeholder>') || field.type == null)
                return true;
        };
        $scope.formatName = function(name) {
            if (typeof name != "undefined") {
                return name.replace(/[^a-z0-9\s]/gi, '');
            } else {
                return "";
            }
        }
        $scope.detectDuplicate = function() {
            $(".duplicate").addClass('ng-hide').each(function() {
                var name = ".d-" + $scope.formatName($(this).attr('fname'));
                var $name = $(name);
                if (name.trim() != ".d-") {
                    if ($name.length > 1) {
                        $(this).removeClass('ng-hide');
                    }
                }
            });
        }
        $scope.refreshColumnPlaceholder = function() {
            $(".cpl").each(function() {
                if ($(this).parent().find("li").length == 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        };
        var timeout = setTimeout(function() {
            $scope.detectDuplicate();
        }, 0);
        $scope.relayout = function(type) {
            if (type == "ColumnField") {
                $scope.refreshColumnPlaceholder();
            }
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                $scope.detectDuplicate();
                $(".field-info").each(function() {
                    var depth = $(this).attr('depth');
                    if (depth > 0) {
                        var that = $(this);
                        for (var i = 0; i < depth) {
                            that = $(that).parentUntil('.form-field').find(".field-info");
                            
                        }
                    }
                });
            }, 0);
        }
        $scope.isCloning = false;
        $scope.isCloneDragging = false;
        $scope.prepareCloneField = function(scope) {
            $scope.isCloning = true;
        }
        $scope.cancelCloneField = function() {
            $timeout(function() {
                if (!$scope.isCloneDragging) {
                    $scope.isCloning = false;
                }
            }, 10);
        }
        $scope.fieldsOptions = {
            dragStart: function(scope) {
                if ($scope.isCloning) {
                    $scope.isCloneDragging = true;
                    scope.elements.placeholder.replaceWith(scope.elements.dragging.clone().find('li:eq(0)'));
                }
            },
            dragStop: function(scope) {
                if ($scope.isCloning) {
                    $scope.isCloning = false;
                    $scope.isCloneDragging = false;
                    var field = angular.copy(scope.source.nodeScope.$modelValue);
                    scope.source.nodesScope.$modelValue.splice(scope.source.index, 0, field);
                }
                $scope.save();
                $timeout(function() {
                    $scope.refreshColumnPlaceholder();
                }, 10);
            }
        };
        $scope.active = null;
        $scope.activeTree = null;

        $scope.save = function() {
            $scope.saving = true;
            $http.post('<?= $this->createUrl("save", array('class' => $class)); ?>', {fields: $scope.fields})
                    .success(function(data, status) {
                        $scope.saving = false;
                        $scope.detectDuplicate();
                    })
                    .error(function(data, status) {
                        $scope.saving = false;
                    });
        };

        $scope.select = function(item) {
            $timeout(function() {
                if ($scope.active == null || item.$modelValue.type != $scope.active.type) {
                    $("#toolbar-properties").hide();
                }
                $scope.inEditor = true;
                $scope.activeTree = item;
                $scope.active = item.$modelValue;
                $scope.tabs.properties = true;
            }, 15);
        };
        $scope.selected = function() {
            $("#toolbar-properties").show();
        }
        $scope.unselect = function() {
            $scope.active = null;
        };
        $scope.unselectViaJquery = function() {
            $scope.$apply(function() {
                $scope.active = null;
                $scope.layout = null;
            });
        };

        $('body').on('click', '.form-field', function(e) {
            $(".form-field.active").removeClass("active");
            if (this == e.target) {
                if ($(this).hasClass('column-placeholder')) {
                    $timeout(function() {
                        $scope.unselect();
                    }, 0);
                    return false;
                }

                $(this).find(".form-field-content:eq(0)").addClass('active').click();

            } else {
                if ($(e.target).hasClass('form-builder-column')) {
                    $timeout(function() {
                        $scope.unselect();
                    }, 0);
                    return false;
                }

                $(this).addClass('active');
                e.stopPropagation();
            }
        });
        $('body').on('click', 'div[ui-header]', function(e) {
            if (e.target == this) {
                $scope.unselectViaJquery();
            }
        });
    });
</script>
