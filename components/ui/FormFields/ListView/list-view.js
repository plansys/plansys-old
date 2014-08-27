app.directive('listView', function($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.update = function() {
                    if (typeof ctrl != 'undefined') {
                        $timeout(function() {
                            ctrl.$setViewValue(angular.copy($scope.value));
                        }, 0);
                    }
                };
                
                $scope.removeItem = function(index) {
                    $scope.value.splice(index, 1);
                    $scope.update();
                }
                $scope.addItem = function() {
                    if (typeof $scope.value != "object") {
                        $scope.value = [''];
                    } else {
                        $scope.value.push('');
                    }
                    $timeout(function() {
                        $el.find('.list-view-item-text').last().focus();
                    }, 0);
                }


                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function() {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.update();
                        }
                    };
                }

                // set default value
                $scope.value = JSON.parse($el.find("data[name=value]").html().trim());
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function() {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                    }, 0);
                }
            }
        }
    };
});