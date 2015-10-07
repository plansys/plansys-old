app.directive('modalDialog', function ($timeout, $compile) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            var renderID = element.find("data[name=render_id]").text();

            $("body > .modal-container." + renderID).remove();
            $container = element.find(".modal-container").remove();
            $container.appendTo('body');
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.renderID = $el.find("data[name=render_id]").text();

                $scope.close = function () {
                    $container.hide();
                };

                $scope.open = function () {
                    $container.show();
                    $container.find(".modal-content").css({
                        maxHeight: $(window).height() - 50,
                        overflowX: $container.find(".modal-content").height() > $(window).height() ? 'auto' : 'visible',
                        overflowY: 'auto'
                    });
                };

                parent[$scope.name] = $scope;
                $compile($container)($scope);

                eval($scope.inlineJS);

            };

        }
    }
});