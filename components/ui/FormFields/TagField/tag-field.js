app.directive('tagField', function ($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                // define vars
                $scope.name = $el.find("data[name=name]:eq(0)").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.dropdown = $el.find("data[name=dropdown]").html().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.renderID = $el.find("data[name=render_id]").html();
                $scope.mustChoose = $el.find("data[name=must_choose]").html();
                $scope.params = $el.find("data[params]").html();
                $scope.valueMode = $el.find("data[name=value_mode]").html().trim();
                $scope.delimiter = $el.find("data[name=delimiter]").html().trim();
                $scope.mapperMode = $el.find("data[name=mapper_mode]").html().trim();
                $scope.fieldOptions = JSON.parse($el.find("data[name=field_options]").html().trim());
                $scope.unique = 'yes';
                $scope.loading = [];
                $scope.parent = $scope.getParent($scope);
                $scope.parent[$scope.name] = $scope;
                $scope.dropdownList = [];
                $scope.tags = [];
                $scope.tagHash = {};
                
                $timeout(function() {
                    $scope.input = $el.find(".tf-input");
                });
                
                $scope.disabled = false;
                if (typeof $scope.fieldOptions.disabled == "string") {
                    $scope.disabled = ($scope.fieldOptions.disabled.toLowerCase() == 'true');
                }
                if ($scope.fieldOptions['ng-disabled']) {
                    $scope.$watch($scope.fieldOptions['ng-disabled'], function(e) {
                        $scope.disabled = !!e;
                    }, true);
                }
                
                
                $scope.inputFocus = function() {
                    $timeout(function(){
                        $scope.input = $el.find(".tf-input");
                        $scope.input.focus();
                    });
                }
                $scope.inputChangeWidth = function() {
                    $timeout(function() {
                        $scope.input = $el.find(".tf-input");
                        var i = $scope.input[0];
                        var w = ((i.value.length + 1) * 7);
                        i.style.width = (Math.max(w,30)) + 'px';
                    });
                }
                $scope.inputKeyup = function(e) {
                    $scope.inputChangeWidth();
                }
                
                $.fn.getCursorPosition = function () {
                    var input = this.get(0);
                    if (!input) return; // No (input) element found
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
                
                $scope.inputKeydown = function(e, idx) {
                    if (e.keyCode == 8 && (e.target.value == "" || $(e.target).getCursorPosition() == 0)) {
                        var tags = getArrayFromValue();
                        
                        if (typeof idx == "undefined") {
                            $scope.removeTagFromValue(tags.length - 1);
                        } else {
                            $scope.removeTagFromValue(idx);
                            
                            $timeout(function(){
                                if ($el.find(".tf-tag").length > 0) {
                                    $el.find(".tf-tag:eq("+(Math.max(idx-1,0))+") .tf-input-edit").focus();
                                } else{
                                    $scope.input.focus();
                                }
                            });
                        }
                        return;
                    }
                    
                    if (e.keyCode == 37) {
                        if ($(e.target).getCursorPosition() == 0) {
                            if ($(e.target).hasClass("tf-input")) {
                                $el.find(".tf-tag:last .tf-input-edit").focus();
                            } else {
                                var target = $el.find(".tf-tag:eq("+(Math.max(idx-1,0))+") .tf-input-edit");
                                target.focus();
                            }
                        }
                    }
                    var focusNext = function() {
                        if ($el.find(".tf-tag").length -1 > idx) {
                            $el.find(".tf-tag:eq("+(Math.max(idx+1,0))+") .tf-input-edit").focus();
                        } else {
                            $scope.input.focus();
                        }
                    }
                    if (e.keyCode == 39) {
                        if ($(e.target).getCursorPosition() == e.target.value.length) {
                            focusNext();
                        }
                    }
                    
                    if (e.keyCode == 13) { 
                        e.preventDefault();
                        e.stopPropagation();

                        $timeout(function() {
                            if (typeof idx == "undefined") {
                                $scope.insertTagToValue(e.target.value);
                                e.target.value = '';
                                $scope.inputFocus();
                            } else if ($scope.tags[idx]) {
                                $scope.updateTagLabel(idx, e.target.value);
                                $timeout(function(){
                                    focusNext();
                                });
                            }
                        });
                    } 

                    $scope.inputChangeWidth();
                }
                $scope.enableEdit = function(t, e) {
                    e.stopPropagation();
                    e.preventDefault();
                    $timeout(function() {
                        t.editing = true;
                    });
                }
                
                $scope.doneEditing = function(i, t, e) {
                    if (e.target.value == '') {
                        $scope.removeTagFromValue(i);
                        
                        $timeout(function(){
                            if ($el.find(".tf-tag").length > 0) {
                                $el.find(".tf-tag:eq("+(Math.max(i-1,0))+") .tf-input-edit").focus();
                            } else{
                                $scope.input.focus();
                            }
                        });
                    }
                    t.editing = false;
                }
                
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                        }
                    };
                }
                
                var isLoading = function(type) {
                    return $scope.loading.indexOf(type) >= 0;
                }
                
                var startLoading = function(type) {
                    if ($scope.loading.indexOf(type) < 0) {
                        $scope.loading.push(type);
                    } 
                }
                
                var finishLoading = function(type) {
                    var idx = $scope.loading.indexOf(type);
                    if (idx >= 0) {
                        $scope.loading.splice(idx, 1);
                    }
                }
                
                var getArrayFromValue = function() {
                    var rawtags = $scope.value.split($scope.delimiter);
                    var tags = [];
                    rawtags.forEach(function(t, i) {
                        t = (t + '').trim();
                        if (!!t) {
                            tags[i] = t;
                        }
                    });
                    return tags;
                }
                
                $scope.updateTagsFromValue = function() {
                    if ($scope.valueMode === 'string') {
                        var value = getArrayFromValue();
                    } else {
                        var value = $scope.value;
                    } 
                    
                    var focus = $el.find(":focus");
                    var idx = 0;
                    if (focus) {
                        if (focus.parent().hasClass("tf-tag")) {
                            idx = focus.parent().attr("idx") * 1;
                        } else {
                            idx = 9999;
                        }
                    }
                    
                    var unique = [];
                    var duplicate = [];
                    
                    $scope.tags.splice(0, $scope.tags.length);
                    for (var i in value) {
                        var v = value[i];
                        var l = $scope.tagHash[v];
                        
                        if (unique.indexOf(v) < 0) {
                            unique.push(v);
                        } else {
                            if ($scope.unique == 'yes') {
                                duplicate.push(v);
                                continue;
                            }
                        }
                        
                        if (($scope.mapperMode == 'none' || $scope.mapperMode == 'insert') && !l) {
                            $scope.tags.push({
                                val: v,
                                label:  v
                            });
                        } 
                    }
                    
                    if ($scope.mapperMode == 'remove' || ($scope.unique == 'yes' && duplicate.length > 0)) {
                        $scope.updateValueFromTags();
                    }
                    
                    if (focus) {
                        $timeout(function() {
                            if (idx == 9999) {
                                $scope.inputFocus();
                            } else {
                                var total = $el.find(".tf-tag").length - 1;
                                $el.find(".tf-tag:eq(" + Math.min(idx, total) + ") .tf-input-edit").focus();
                            }
                        } ,300);
                    }
                }

                $scope.updateTagLabel = function(idx, label) {
                    if ($scope.mapperMode != 'none') {
                        var found = false;
                        for (var i in $scope.tagHash) {
                            if ($scope.tagHash[i] == val) {
                                $scope.tags[idx].val = val;
                                $scope.updateValueFromTags();
                                found = true;
                                break;
                            }
                        }
                        
                        if (!found) {
                            if (!isLoading('map-tag')) {
                                startLoading('map-tag');
                                $http.post(Yii.app.createUrl('formfield/TagField.mapTag'), {
                                    m: $scope.modelClass,
                                    n: $scope.name,
                                    l: [val],
                                    v: []
                                }).success(function(data) {
                                    finishLoading('map-tag');
                                    for (var i in data) {
                                        $scope.tagHash[i] = data[i].trim() + '';
                                        if (data[i] === val) {
                                            $scope.tags[idx].val = i;
                                        }
                                        $scope.updateValueFromTags();
                                    }
                                });
                            }
                        }
                    } else {
                        $scope.tags[idx].val = label;
                        $scope.updateValueFromTags();
                    }
                }
                
                $scope.insertTagToValue = function(val) { // will trigger value watcher
                    if (!!val) {
                        if ($scope.valueMode === 'string') {
                            var tags = getArrayFromValue();
                            tags.push(val);
                            $scope.value = tags.join($scope.delimiter);
                        } else {
                            $scope.value.push(val);
                        }
                    }
                }
                $scope.updateValueFromTags = function() { // will trigger value watcher
                    if ($scope.valueMode === 'array') {
                        $scope.value.splice(0, $scope.value.length - 1);
                        var val = $scope.value;
                    } else {
                        var val = [];
                    }
                    
                    for (var i in $scope.tags) {
                        val.push($scope.tags[i].val);
                    }
                    
                    if ($scope.valueMode === 'string') {
                        $scope.value = val.join($scope.delimiter);
                    }
                }
                
                
                $scope.removeTagFromValue = function(idx) { // will trigger value watcher
                    if ($scope.valueMode === 'string') {
                        var tags = getArrayFromValue();
                        tags.splice(idx, 1);
                        $scope.value = tags.join($scope.delimiter);
                    } else {
                        $scope.value.splice(idx, 1);
                    }
                }
                
                $scope.init = function() {
                    $scope.$watch('value', function(e) {  // value watcher, will invoke tagmapper
                        if ($scope.valueMode == 'string') {
                            if (typeof e === 'string') {
                                var rawtags = e.split($scope.delimiter);
                                var tags = [];
                                var unmappedVal = [];
                                rawtags.forEach(function(t, idx) {
                                    t = (t + '').trim();
                                    if (!!t) {
                                        tags[idx] = t;
                                        if (!$scope.tagHash[t]) {
                                            unmappedVal.push(t);
                                        }
                                    }
                                });

                                if (unmappedVal.length > 0 && $scope.mapperMode != 'none') {
                                    if (!isLoading('map-tag')) {
                                        startLoading('map-tag');
                                        $http.post(Yii.app.createUrl('formfield/TagField.mapTag'), {
                                            m: $scope.modelClass,
                                            n: $scope.name,
                                            v: unmappedVal,
                                            l: []
                                        }).success(function(data) {
                                            finishLoading('map-tag');
                                            for (var i in data) {
                                                var idx = tags.indexOf(i);
                                                $scope.tagHash[i] = data[i].trim() + '';
                                            }
                                            $scope.updateTagsFromValue();
                                        });
                                    }
                                } else {
                                    $scope.updateTagsFromValue();
                                }
                            }
                        }
                        
                        ctrl.$setViewValue($scope.value);
                    });
                    
                    if ($scope.dropdown == 'normal') {
                        startLoading('map-tag');
                        $http.post(Yii.app.createUrl('formfield/TagField.getList'), {
                            m: $scope.modelClass,
                            n: $scope.name
                        }).success(function(data) {
                            finishLoading('map-tag');
                            $scope.list = data;
                        });
                    } 
                }
                
                $scope.init();
                
            };
        }
    };
});