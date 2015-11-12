app.directive('popupWindow', function ($timeout, $http, $interpolate) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                $scope.parent = parent;
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.subForm = $el.find("data[name=subform]:eq(0)").text();
                $scope.title = $el.find("data[name=title]:eq(0)").text();
                $scope.url = $el.find("data[name=url]:eq(0)").text();
                $scope.mode = $el.find("data[name=mode]:eq(0)").text();
                $scope.parentForm = $el.find("data[name=parent_form]:eq(0)").text();
                $scope.options = JSON.parse($el.find("data[name=options]:eq(0)").text());

                if (!$scope.options.width) {
                    $scope.options.width = '500';
                }
                if (!$scope.options.height) {
                    $scope.options.height = '300';
                }

                $scope.open = function () {
                    var url = Yii.app.createUrl('/formfield/PopupWindow.subform', {
                        c: $scope.parentForm || $scope.formClassPath,
                        f: $scope.name
                    });

                    if ($scope.mode == 'url') {
                        url = Yii.app.createUrl($interpolate($scope.url)($scope));
                    } 

                    PopupCenter(url, $scope.title, $scope.options.width, $scope.options.height);
                }
                parent[$scope.name] = $scope;
                window.formScope = parent;
            };
        }
    };
});