app.directive('sqlCriteria', function ($timeout, $compile, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.paramsField = $el.find("data[name=params_field]").text();
                $scope.inlineJS = $el.find("pre[name=inline_js]:eq(0)").text();
                $scope.baseClass = $el.find('data[name=base_class]').text();
                $scope.value = $scope.$parent.active[$scope.name];
                $scope.isError = false;
                $scope.isLoading = true;
                $scope.errorMsg = '';
                $scope.previewSQL = '';
                $scope.modelClass = '';

                $scope.getPreviewSQL = function () {
                    $scope.isLoading = true;
                    var postparam = {
                        criteria: $scope.value,
                        params: $scope.active[$scope.paramsField],
                        baseclass: $scope.baseClass
                    };

                    switch ($scope.baseClass) {
                        case "DataSource":
                            postparam.rel = $scope.$parent.active.relationTo;
                            postparam.dsname = $scope.$parent.active.name;
                            postparam.dsclass = $scope.$parent.classPath;
                            break;
                        case "RelationField":
                            postparam.rfname = $scope.$parent.active.name;
                            postparam.rfclass = $scope.$parent.classPath;
                            postparam.rfmodel = $scope.$parent.active.modelClass;
                            break;
                        case "DataGrid":
                        case "DataFilter":
                            postparam.params = $scope.item[$scope.paramsField] || {};
                            postparam.rfname = $scope.$parent.active.name;
                            postparam.rfclass = $scope.$parent.classPath;
                            postparam.rfmodel = $scope.modelClass;
                            break;
                    }

                    url = Yii.app.createUrl('/formfield/SqlCriteria.previewSQL');
                    $http.post(url, postparam).success(function (data) {
                        $scope.previewSQL = data.sql;
                        $scope.errorMsg = data.error;
                        $scope.isError = ($scope.errorMsg != '');
                        $scope.isLoading = false;
                    });
                }

                $scope.$watch('active.' + $scope.paramsField, function (newv, oldv) {
                    if (newv != oldv) {
                        $timeout(function () {
                            $scope.getPreviewSQL();
                        }, 0);
                    }
                }, true);

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
