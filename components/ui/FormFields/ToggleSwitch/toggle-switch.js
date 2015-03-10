app.directive('toggleSwitch', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (typeof ctrl != 'undefined') {
                        if ($scope.value === true || $scope.value == $scope.onLabel) {
                            ctrl.$setViewValue(true);
                        } else {
                            ctrl.$setViewValue(false);
                        }
                    }
                };

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.update();
                        }
                    };
                }

                $scope.switch = function () {
                    $scope.value = !$scope.value;
                    $scope.update();
                };

                // set default value
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.onLabel = $el.find("data[name=on_label]").html();
                $scope.offLabel = $el.find("data[name=off_label]").html();
                $scope.options = JSON.parse($el.find("data[name=options]").text());

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
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