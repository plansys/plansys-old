app.directive('checkBoxList', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                var rel = JSON.parse($el.find("data[name=rel_info]:eq(0)").text());
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.mode = $el.find("data[name=mode]:eq(0)").text();
                parent[$scope.name] = $scope;
                $scope.relHash = {};
                $scope.insertData = [];
                $scope.insertHash = {};
                $scope.deleteData = [];
                $scope.deleteHash = {};
                $scope.originalData = [];
                $scope.relOriginalHash = null;
                
                $scope.resetRelHash = function() {
                    $scope.relHash = {};
                    for (i in ctrl.$viewValue) {
                        $scope.relHash[ctrl.$viewValue[i][rel.targetKey]] = ctrl.$viewValue[i];
                    }
                    
                    $scope.deleteHash = {};
                    for (i in $scope.deleteData) {
                        $scope.deleteHash[$scope.deleteData[i][rel.targetKey]] = {
                            idx: i,
                            data: $scope.deleteData[i]
                        };
                    }
                    
                    $scope.insertHash = {};
                    for (i in $scope.insertData) {
                        $scope.insertHash[$scope.insertData[i][rel.targetKey]] = {
                            idx: i,
                            data: $scope.insertData[i]
                        };
                    }
                    
                    if ($scope.relOriginalHash === null) {
                        $scope.relOriginalHash = {};
                        for (i in $scope.originalData) {
                            $scope.relOriginalHash[$scope.originalData[i][rel.targetKey]] = {
                                idx: i,
                                data: $scope.originalData[i]
                            }
                        }
                    }
                }

                $scope.updateItem = function (value) {
                    $scope.updateItemInternal(value);
                    if (typeof ctrl != 'undefined' && value) {
                        if ($scope.mode == "Relation") {
                            $scope.resetRelHash();
                        }
                        $timeout(function () {
                            if ($scope.mode == "Default") {
                                ctrl.$setViewValue($scope.selectedText);
                            } else if ($scope.mode == "Relation") {
                                ctrl.$setViewValue($scope.selected);
                            }
                        }, 0);
                    }
                };
                
                $scope.isChecked = function(value) {
                    if ($scope.mode == "Default") {
                        if ($scope.selected == null) return false;
                        return ($scope.selected.indexOf(value) > -1)
                    } else if ($scope.mode == "Relation") {
                        return !!$scope.relHash[value];
                    }
                }

                $scope.updateItemInternal = function (value) {
                    if (typeof value != 'undefined') {
                        if ($scope.selected == null) {
                            $scope.selected = [];
                        }
                        
                        var ar = $scope.selected;
                        if (!angular.isArray(ar) && $scope.mode == "Relation") {
                            $scope.selected = ar = [];
                        }
                        
                        if (angular.isArray(ar)) {
                            if ($scope.mode == "Default") {
                                if (ar.indexOf(value) >= 0) {
                                    ar.splice(ar.indexOf(value), 1);
                                    $scope.selectedText = ar.join(",");
                                } else {
                                    ar.push(value.replace(/,/g, ''));
                                    $scope.selectedText = ar.join(",");
                                }
                            } else if ($scope.mode == "Relation") {
                                // uncheck item
                                if ($scope.isChecked(value)) {
                                    for (i in ar) {
                                        if (ar[i][rel.targetKey] == value) {
                                            var item = ar.splice(i, 1);
                                            if (!$scope.deleteHash[value]) {
                                                if (!!$scope.relOriginalHash[value] && item[0].$rowState != "insert") {
                                                    item[0].$rowState = "remove";
                                                    $scope.deleteData.push(item[0]);
                                                }
                                            }
                                            if (!!$scope.insertHash[value]) {
                                                $scope.insertData.splice($scope.insertHash[value].idx, 1);
                                            }
                                            break;
                                        }
                                    }
                                } else {
                                    // check item
                                    var item = $scope.relOriginalHash[value];
                                    if (!item) {
                                        item = angular.copy(rel.attributes);
                                        item[rel.foreignKey] = $scope.model[rel.parentPrimaryKey];
                                        item[rel.targetKey] = value;
                                    } else {
                                        item = item.data;
                                    }
                                    item.$rowState = "insert";
                                    ar.push(item);
                                    
                                    if (!!$scope.deleteHash[value]) {
                                        $scope.deleteData.splice($scope.deleteHash[value].idx, 1);
                                    }
                                    if (!$scope.insertHash[value]) {
                                        if (!$scope.relOriginalHash[value]) {
                                            $scope.insertData.push(item);
                                        }
                                    }
                                }
                                $scope.resetRelHash();
                            }
                        }
                    }
                }

                // when ng-model, or ps-list is changed from outside directive
                if (attrs.psList) {
                    //ps-list, replace entire list using js instead of rendered from server
                    function changeFieldList() {
                        $timeout(function () {
                            $scope.formList = $scope.$eval(attrs.psList);
                            $scope.updateItemInternal($scope.value);
                        }, 0);
                    }
                    $scope.$watch(attrs.psList, changeFieldList);
                }

                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;
                            
                        if (typeof ctrl.$viewValue == 'string') {
                            $scope.selected = [];
                            ctrl.$viewValue.split(',').map(function (item) {
                                $scope.selected.push(item);
                                $scope.selectedText = $scope.selected.join(",");
                            });
                        }
                    };
                }

                $timeout(function () {
                    // set default value
                    $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                    $scope.selected = JSON.parse($el.find("data[name=selected]").text());
                    if ($scope.mode == 'Relation') {
                        $scope.deleteData = JSON.parse($el.find("data[name=delete_data]").text());
                        $scope.modelClass = $el.find("data[name=model_class]").html();
                        
                        $scope.selected = ctrl.$viewValue;
                        $scope.originalData = angular.copy(ctrl.$viewValue);
                        
                        for (i in $scope.originalData) {
                            if ($scope.originalData[i].$rowState == 'insert') $scope.insertData.push($scope.originalData[i]);
                        }
                        
                        for (i in $scope.deleteData) {
                            $scope.deleteData.push($scope.deleteData[i]);
                            if (!!$scope.relOriginalHash[$scope.deleteData[i][$scope.targetKey]]) {
                                $scope.selected.splice($scope.relOriginalHash[$scope.deleteData[i][$scope.targetKey]].idx, 1);
                            }
                        }
                        
                        $scope.resetRelHash();
                    } else {
                        if (attrs.ngModel) {
                            $scope.selected = $scope.$eval(attrs.ngModel);
                        }
    
                        if (typeof $scope.selected == "string") {
                            $scope.selected = $scope.selected.split(',').map(function (item) {
                                return(item.trim());
                            });
                        }
                        if ($scope.selected !== null && angular.isArray($scope.selected)) {
                            $scope.selectedText = $scope.selected.join(',');
                        }
                    }
                });
            }
        }
    };
});