app.directive('repoBrowser', function ($timeout, $compile, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {

            if ($("body > [repo-dialog]").length == 0) {
                $("body").append('<div repo-dialog></div>');
            }

            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                if ($("body > [repo-dialog] .modal-container").length == 0) {
                    $compile($("body > [repo-dialog]"))($scope.$parent);
                }
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.renderID = $el.find("data[name=render_id]").text();

                var a = setInterval(function () {
                    $scope.dialog = angular.element($("body > [repo-dialog] .modal-container")[0]).scope();
                    if ($scope.dialog) {
                        clearInterval(a);
                        $scope.open = function () {
                            if ($scope.afterChoose) {
                                $scope.dialog.afterChoose = $scope.afterChoose;
                            }
                            $scope.dialog.open();
                        }
                        $scope.close = function () {
                            $scope.dialog.close();
                        }
                        parent[$scope.name] = $scope;
                    }
                }, 100);

            }
        }
    };
});