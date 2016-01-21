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
                    $timeout(function () {
                        if (!!ctrl) {
                            if ($scope.valueCheckbox === true) {
                                $scope.valueCheckbox = true;
                                $scope.value = $scope.onLabel;
                            } else {
                                $scope.valueCheckbox = false;
                                $scope.value = $scope.offLabel;
                            }
                            ctrl.$setViewValue($scope.value);
                        }
                    });
                };

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.valueCheckbox = ctrl.$viewValue == $scope.onLabel;
                            $scope.update();
                        }
                    };
                }

                $scope.switch = function () {
                    if (!$($el).find('input[type=checkbox]').attr('disabled')) {
                        $scope.valueCheckbox = !$scope.valueCheckbox;
                        $scope.update();
                    }
                };

                // set default value
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.options = JSON.parse($el.find("data[name=options]").text());
                $scope.onLabel = $el.find("data[name=on_label]").html();
                $scope.offLabel = $el.find("data[name=off_label]").html();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.valueCheckbox = $scope.value == $scope.onLabel;

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                    });
                }
            }
        }
    };
});