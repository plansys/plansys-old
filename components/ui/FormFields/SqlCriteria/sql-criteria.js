app.directive('sqlCriteria', function ($timeout, $compile, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.name = $el.find("data[name=name]").text();
                $scope.inlineJS = $el.find("pre[name=inline_js]:eq(0)").text();
                $scope.value = JSON.parse($el.find("data[name='value']:eq(0)").text().trim());
                
                $scope.previewSQL = '';
                $scope.modelClass = '';

                $scope.getPreviewSQL = function () {
                    url = Yii.app.createUrl('/FormField/SqlCriteria.previewSQL');
                    $http.post(url, {
                        class: $scope.modelClass,
                        criteria: $scope.value.criteria
                    }).success(function (data) {
                        $scope.previewSQL = data;
                    });
                }

                $scope.$watch('modelClass', function (newv) {
                    if (newv != '' && newv) {
                        $scope.getPreviewSQL();
                    }
                });
                
                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (typeof ctrl != 'undefined') {
                        $timeout(function () {
                            ctrl.$setViewValue($scope.value);
                            $scope.getPreviewSQL();

                        }, 0);
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

                eval($scope.inlineJS);
            }
        }
    }
});
