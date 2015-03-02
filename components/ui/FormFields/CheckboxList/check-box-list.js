app.directive('checkBoxList', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.$parent[$scope.name] = $scope;

                $scope.updateItem = function (value) {
                    $scope.updateItemInternal(value);
                    if (typeof ctrl != 'undefined' && value) {
                        $timeout(function () {
                            ctrl.$setViewValue($scope.selectedText);
                        }, 0);
                    }
                };

                $scope.updateItemInternal = function (value) {
                    if (typeof value != 'undefined') {
                        console.log(value, $scope.selected);
                        if($scope.selected == null){
                            $scope.selected = [];
                        }
                        var ar = $scope.selected;
                        if (ar.indexOf(value) >= 0) {
                            ar.splice(ar.indexOf(value), 1);
                            $scope.selectedText = ar.join(",");
                        } else {
                            ar.push(value.replace(/,/g, ''));
                            $scope.selectedText = ar.join(",");
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

                if (typeof ctrl != 'undefined') {
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
                    $scope.modelClass = $el.find("data[name=model_class]").html();

                    if (attrs.ngModel) {
                        $scope.selected = $scope.$eval(attrs.ngModel);
                    }

                    if (typeof $scope.selected == "string") {
                        $scope.selected = $scope.selected.split(',').map(function (item) {
                            return(item.trim());
                        });
                    }
                    if($scope.selected !== null){
                        $scope.selectedText = $scope.selected.join(',');
                    }

                });
            }
        }
    };
});