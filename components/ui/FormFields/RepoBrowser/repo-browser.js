app.directive('repoBrowser', function ($timeout, $compile, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            $a = element.find(".repo-dialog").remove();
            if ($("body > .repo-dialog").length == 0) {
                $a.attr('repo-dialog', '');
                $("body").append($a);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.name = $el.find("data[name=name]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();

                $a = setInterval(function () {
                    $scope.dialog = angular.element($("body > .repo-dialog .modal-container")[0]).scope();
                    if ($scope.dialog) {
                        clearInterval($a);
                        $scope.open = function () {
                            if ($scope.afterChoose) {
                                $scope.dialog.afterChoose = $scope.afterChoose;
                            }
                            $scope.dialog.open();
                        }
                        $scope.close = function () {
                            $scope.dialog.close();
                        }
                    }
                }, 10);

                $scope.$parent[$scope.name] = $scope;
            }
        }
    };
});