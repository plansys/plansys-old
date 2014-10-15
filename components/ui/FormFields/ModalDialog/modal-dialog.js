app.directive('modalDialog', function ($timeout, $compile) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            var renderID = element.find("data[name=render_id]").text();

            $("body > .modal-container." + renderID).remove();
            $container = element.find(".modal-container").remove();
            $container.appendTo('body');

            return function ($scope, $el, attrs, ctrl) {
                $scope.name = $el.find("data[name=name]").text();
                $scope.renderID = $el.find("data[name=render_id]").text();

                $scope.close = function () {
                    $container.hide();
                };

                $scope.open = function () {
                    $container.show();
                };
                
                $scope.$parent[$scope.name] = $scope;
                $compile($container)($scope);
            };
        }
    }
});