app.directive('radioButtonList', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.name = $el.find("data[name=name]:eq(0)").text();

                // when ng-model is changed from inside directive
                $scope.update = function (val) {
                    $scope.value = val;
                    if (!!ctrl) {
                        ctrl.$setViewValue($scope.value);
                    }
                }

                // when ng-model is changed from outside directive
                if (attrs.psList) {
                    function changeFieldList() {
                        $timeout(function () {
                            $scope.formList = $scope.$eval(attrs.psList);
                        }, 0);
                    }
                    $scope.$watch(attrs.psList, changeFieldList);
                }

                if (!!ctrl) {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $el.find('input[type=radio]:checked').removeAttr('checked');
                            $el.find('input[type=radio][value="' + ctrl.$viewValue + '"]').attr('checked', 'checked');
                        }
                    };
                }

                // set default value
                $scope.formList = JSON.parse($el.find("data[name=form_list]").text());
                $scope.value = $el.find('data[name=value]').text().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();

                //if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                    }, 0);
                }
            };
        }
    };
});