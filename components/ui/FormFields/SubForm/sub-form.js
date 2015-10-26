app.directive('subForm', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                $scope.parent = parent;

                // set default value
                $scope.value = JSON.parse($el.find("data[name=value]").html().trim());
                $scope.modelClass = $el.find("data[name=model_class]").html().trim();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.mode = $el.find("data[name=mode]:eq(0)").text().trim();
                $scope.templateAttr = JSON.parse($el.find("data[name=template_attr]").html().trim());
                $scope.options = JSON.parse($el.find("data[name=options]").html().trim());

                // when ng-model is changed from outside directive
                if (!!ctrl) {
                    ctrl.$ctrlView = $scope.$eval(attrs.ngModel);
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.loading = true;
                            $scope.value = ctrl.$viewValue;
                            $timeout(function () {
                                $scope.loading = false;
                            }, 0);
                        }
                    };

                    if ($scope.mode == 'single') {
                        if ($scope.inEditor) {
                            $scope.active = ctrl.$ctrlView;
                        } else {
                            $scope.model = ctrl.$ctrlView;
                        }
                    }
                }
            };
        }
    };
});