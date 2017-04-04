app.directive('tagField', function ($timeout, $http, $q) {
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
                $scope.suggestion = $el.find("data[name=suggestion]").html().trim();
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
                $scope.ref = $el.find("data[name=ref]").html().trim();
                $scope.suggestionMode = $el.find("data[name=sug_mode]").html().trim();
                $scope.showSuggestion = false;
                $scope.suggestion = [];
                $scope.sugIdx = ($scope.mustChoose == 'yes' ? 0 : -1);
                
                $timeout(function() {
                    $scope.input = $el.find(".tf-input");
                });
                
                $scope.resetSugIdx = function() {
                    $scope.sugIdx = ($scope.mustChoose == 'yes' ? 0 : -1);
                }
                
                $scope.disabled = false;
                if (typeof $scope.fieldOptions.disabled == "string") {
                    $scope.disabled = ($scope.fieldOptions.disabled.toLowerCase() == 'true');
                }
                if ($scope.fieldOptions['ng-disabled']) {
                    $scope.$watch($scope.fieldOptions['ng-disabled'], function(e) {
                        $scope.disabled = e;
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
                        var w = ((i.value.length + 1) * 9);
                        i.style.width = (Math.max(w,150)) + 'px';
                    });
                }
                $scope.inputKeyup = function(e, idx) {
                    if (e.keyCode == 13) {
                        $scope.hideSuggestion();
                    } 
                    else if ($scope.suggestionMode == 'php' && $scope.reloadSug != e.target.value) {
                        if ($scope.getSugHttp) {
                            $scope.getSugHttp.resolve();
                        }
                        $scope.getSugHttp = $q.defer();
                        $http.post(Yii.app.createUrl('formfield/TagField.getSug'), {
                            m: $scope.modelClass,
                            n: $scope.name,
                            s: e.target.value,
                            mdl: $scope.$parent.model,
                            prm: $scope.$parent.params,
                            ref: $scope.ref
                        },{
                            timeout: $scope.getSugHttp.promise
                        }).success(function(data) {
                            $scope.getSugHttp = null;
                            $scope.showSuggestion = false;
                            $scope.suggestion = {};
                            if (typeof data != 'string') {
                                var isobj = typeof data === 'object' && !(data instanceof Array);
                                var value = getArrayFromValue();
                                for (var i in data) {
                                    if (isobj && !$scope.tagHash[i]) {
                                        $scope.tagHash[i] = data[i];
                                    }
                                    
                                    if ($scope.unique == 'yes') {
                                        if (value.indexOf(i) >= 0) {
                                            if (typeof idx == undefined) 
                                                continue;
                                            
                                            if ($scope.value[idx] != i) 
                                                continue;
                                        } 
                                    
                                        $scope.suggestion[i] = data[i]; 
                                        $scope.showSuggestion = true;
                                    
                                    } else {
                                        $scope.suggestion[i] = data[i]; 
                                        $scope.showSuggestion = true;
                                    }
                                }
                                
                                if ($scope.showSuggestion === true) {
                                    $scope.showSuggestion = typeof idx == 'undefined' ? 'new' : idx;
                                } 
                            }
                        });
                    }
                    
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
                    
                    $scope.reloadSug = e.target.value;
                    if ($scope.suggestionMode == 'php') {
                        if (e.keyCode == 40) {
                            e.preventDefault();
                            e.stopPropagation();
                            $scope.sugIdx++;
                            if ($scope.sugIdx > $el.find(".dropdown-item").length - 1) {
                                $scope.resetSugIdx();
                            }
                        } else if (e.keyCode == 38) {
                            e.preventDefault();
                            e.stopPropagation();
                            $scope.sugIdx--;
                            if ($scope.sugIdx < 0) {
                                $scope.sugIdx = $el.find(".dropdown-item").length - 1;
                            }
                        }
                    }
                    
                    if (e.keyCode == 13) { 
                        e.preventDefault();
                        e.stopPropagation();
                        $timeout(function() {
                            if ($scope.suggestionMode == 'php' && $scope.mustChoose == 'yes') {
                                var sug = $el.find(".dropdown-item:eq(" + $scope.sugIdx + ")");
                                var label = sug.attr('l');
                                var val = sug.attr('v');
                                if (!!label && !!val) {
                                    if (typeof idx == "undefined") {
                                        $scope.updateTagLabel(null, label, val);
                                        e.target.value = '';
                                        $scope.inputFocus();
                                        $scope.resetSugIdx();
                                    } else if ($scope.tags[idx]) {
                                        $scope.updateTagLabel(idx, label, val);
                                        $timeout(function(){
                                            $scope.resetSugIdx();
                                            focusNext();
                                        });
                                    }
                                    $scope.hideSuggestion();
                                }
                            } else if($scope.suggestionMode == 'php' && $scope.mustChoose != 'yes') {
                                var label = e.target.value;
                                var val = $scope.mapperMode == 'none' ? e.target.value : undefined;
                                
                                if ($scope.suggestionMode == 'php') {
                                    var sug = $el.find(".dropdown-item:eq(" + $scope.sugIdx + ")");
                                    if (sug.length > 0) {
                                        label = sug.attr('l');
                                        val = sug.attr('v');
                                    }
                                }
                                
                                if (typeof idx == "undefined") {
                                    $scope.updateTagLabel(null, label, val);
                                    e.target.value = '';
                                    $scope.inputFocus();
                                } else if ($scope.tags[idx]) {
                                    $scope.updateTagLabel(idx, label, val);
                                    $timeout(function(){
                                        focusNext();
                                    });
                                }
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
                
                $scope.hideSuggestion = function() {
                    $scope.showSuggestion = false;
                    $scope.showIdx = 0;
                    if ($scope.doneEditingTimeout) {
                        $timeout.cancel($scope.doneEditingTimeout);
                    }
                }
                
                $scope.doneEditingTimeout = null;
                $scope.doneEditing = function(i, t, e) {
                    if (!!t) {
                        // untuk mencegah suggestion muncul ditempat yg tidak diinginkan
                        if (!!i && e.target.value == '' 
                                && ($scope.suggestionMode != 'none' && i != $scope.showSuggestion)) {
                            $scope.removeTagFromValue(i);
                            $timeout(function(){
                                if ($el.find(".tf-tag").length > 0) {
                                    $el.find(".tf-tag:eq("+(Math.max(i-1,0))+") .tf-input-edit").focus();
                                } else{
                                    $scope.input.focus();
                                }
                            });
                        }
                        
                        // ketika selesai ngedit, balikin label nya sesuai dengan value yg ada di hash
                        if ($scope.suggestionMode == 'php' && $scope.mustChoose == 'yes') {
                            t.label = $scope.tagHash[t.val];
                        }
                        t.editing = false;
                    } else {
                        e.target.value = '';
                    }
                    
                    $scope.doneEditingTimeout = $timeout(function() {
                        $scope.hideSuggestion();
                    }, 1000);
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
                    if ($scope.valueMode == 'string') {
                        var rawtags = $scope.value.split($scope.delimiter);
                    } else {
                        var rawtags = $scope.value;
                    }
                    if (!rawtags) {
                        rawtags = [];
                    }
                    
                    var tags = [];
                    rawtags.forEach(function(t, i) {
                        t = (t + '').trim();
                        if (!!t) {
                            tags[i] = t;
                        }
                    });
                    return tags;
                }
                
                $scope.chooseItem = function(idx, label, value) {
                    $scope.updateTagLabel(idx, label, value);
                    $scope.inputFocus();
                    $scope.hideSuggestion();
                }
                
                $scope.chooseNewItem = function(label, value) {
                    $scope.updateTagLabel(null, label, value);
                    $el.find(".tf-input").val('');
                    $scope.inputFocus();
                    $scope.hideSuggestion();
                }
                
                $scope.updateTagsFromValue = function(shouldFocus) {
                    if ($scope.valueMode === 'string') {
                        var value = getArrayFromValue();
                    } else {
                        var value = $scope.value;
                    } 
                    
                    if (typeof shouldFocus == 'undefined') {
                        shouldFocus = true;
                    }
                    
                    var focus = $el.find(":focus");
                    var idx = 0;
                    var unique = [];
                    var duplicate = [];
                    if (focus) {
                        if (focus.parent().hasClass("tf-tag")) {
                            idx = focus.parent().attr("idx") * 1;
                        } else {
                            idx = 9999;
                        }
                    }
                    
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
                        
                        if (($scope.mapperMode == 'none' || $scope.mapperMode == 'insert')) {
                            $scope.tags.push({
                                val: v,
                                label: typeof l != 'undefined' ? l : v
                            });
                        } 
                    }
                    
                    if ($scope.mapperMode == 'remove' || ($scope.unique == 'yes' && duplicate.length > 0)) {
                        $scope.updateValueFromTags();
                    }
                    
                    if (shouldFocus && focus) {
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

                $scope.updateTagLabel = function(idx, label, value) {
                    if (!!label && !!value) {
                        $scope.tagHash[value] = label;
                    }
                    
                    if ($scope.mapperMode != 'none') {
                        var found = false;
                        for (var i in $scope.tagHash) {
                            if ($scope.tagHash[i] == label) {
                                if (idx === null) {
                                    $scope.tags.push({
                                        val:i, 
                                        label: $scope.tagHash[i]
                                    });
                                } else {
                                    $scope.tags[idx].val = i;
                                    $scope.tags[idx].label = $scope.tagHash[i];
                                }
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
                                    l: [label],
                                    v: [],
                                    mdl: $scope.$parent.model,
                                    prm: $scope.$parent.params,
                                    ref: $scope.ref
                                }).success(function(data) {
                                    finishLoading('map-tag');
                                    for (var i in data) {
                                        $scope.tagHash[i] = data[i].trim() + '';
                                        if ($scope.tagHash[i] === label) {
                                            if (idx === null) {
                                                $scope.tags.push({
                                                    val:i, 
                                                    label: $scope.tagHash[i]
                                                });
                                            } else {
                                                $scope.tags[idx].val = i;
                                                $scope.tags[idx].label = $scope.tagHash[i];
                                            }
                                            found = true;
                                        }
                                    }
                                    
                                    if (!found && $scope.mapperMode == 'insert') {
                                        if (idx === null) {
                                            $scope.tags.push({val:label});
                                        } else {
                                            $scope.tags[idx].val = label;
                                        }
                                    }
                                    
                                    $scope.updateValueFromTags();
                                });
                            }
                        }
                    } else {
                        if (idx === null) {
                            var duplicate = false;
                            if ($scope.unique == 'yes') {
                                for (var t in $scope.tags) {
                                    if ($scope.tags[t].val == value) {
                                        duplicate = true;
                                    }
                                }
                            }
                            
                            if (!duplicate) {
                                $scope.tags.push({
                                    label:label,
                                    val:value
                                });
                            }
                        } else {
                            $scope.tags[idx].label = label;
                            $scope.tags[idx].val = value;
                        }
                        
                        $scope.updateValueFromTags();
                    }
                    
                    if ($scope.suggestionMode == 'php') {
                        if (value && $scope.suggestion[value]) {
                            delete $scope.suggestion[value]
                        } else if (label) {
                            for (var i in $scope.suggestion) {
                                if ($scope.suggestion[i] === label) {
                                    delete $scope.suggestion[i];
                                    break;
                                }
                            }
                        }
                        
                        var showSug = false;
                        for (var i in $scope.suggestion) {
                            showSug = true;
                            break;
                        }
                        
                        if (!showSug) {
                            $scope.showSuggestion = false;
                        }
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
                var svtimeout = null;
                $scope.setValue = function(value) {
                    ctrl.$setViewValue(angular.copy(value));
                }
                $scope.updateValueFromTags = function() { // will trigger value watcher
                    if ($scope.valueMode === 'array') {
                        if (!$scope.value) {
                            $scope.value = [];
                        }
                        $scope.value.splice(0, $scope.value.length);
                        var val = $scope.value;
                    } else {
                        var val = [];
                    }
                    
                    for (var i in $scope.tags) {
                        if ($scope.unique == 'yes') {
                            if (val.indexOf($scope.tags[i].val) >= 0) {
                                continue;
                            }
                        }
                        val.push($scope.tags[i].val);
                    }
                    
                    if ($scope.valueMode === 'string') {
                        $scope.value = val.join($scope.delimiter);
                    }
                    $scope.setValue($scope.value);
                }
                
                $scope.removeTagFromValue = function(idx) { // will trigger value watcher
                    if ($scope.valueMode === 'string') {
                        var tags = getArrayFromValue();
                        tags.splice(idx, 1);
                        $scope.value = tags.join($scope.delimiter);
                    } else {
                        if (!!$scope.value) {
                            $scope.value.splice(idx, 1);
                            $scope.updateTagsFromValue();
                        }
                    }
                    $scope.setValue($scope.value);
                }
                
                $scope.init = function() {
                    $scope.$watch(function() { return ctrl.$viewValue}, function(e) {
                        ctrl.$setViewValue($scope.value);
                        $scope.updateTagsFromValue(false);
                    },true);
                    
                    $scope.$watch('value', function(e) {  // value watcher, will invoke tagmapper
                        if ($scope.valueMode == 'string' && typeof e === 'string') {
                            var rawtags = e.split($scope.delimiter);
                        } else {
                            var rawtags = e;
                        }
                        
                        if (rawtags && rawtags.length > 0) {
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
                                        l: [],
                                        mdl: $scope.$parent.model,
                                        prm: $scope.$parent.params,
                                        ref: $scope.ref
                                    }).success(function(data) {
                                        finishLoading('map-tag');
                                        for (var i in data) {
                                            var idx = tags.indexOf(i);
                                            $scope.tagHash[i] = data[i].trim() + '';
                                        }
                                        $scope.updateTagsFromValue();
                                        $scope.updateValueFromTags();
                                    });
                                }
                            } else {
                                $scope.updateTagsFromValue();
                                $scope.updateValueFromTags();
                            }
                        }
                    });
                }
                $scope.init();
                
            };
        }
    };
});