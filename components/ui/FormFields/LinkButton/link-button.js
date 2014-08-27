app.directive('linkBtn', function($timeout, $parse) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {
            return function($scope, $el, attrs, ctrl) {

                if ($el.attr('group') != '' && $(".link-btn[group=" + $el.attr('group') + "]").length > 1) {
                    $firstBtn = $(".link-btn[group=" + $el.attr('group') + "]").eq(0);
                    if (!$firstBtn.parent().hasClass('btn-group')) {
                        $firstBtn.wrap("<div class='btn-group'></div>");
                    }

                    $el.css('opacity', '1').appendTo($firstBtn.parent());
                } else {
                    $el.css('opacity', '1');
                }

                $scope.url = $el.attr('url');
                $scope.urlparams = $el.find("data[name=urlparams]").html();
                
                $el.on('click', function() {
                    if (!$el.hasClass('on-editor') && typeof $el.attr('ng-click') == "undefined") {
                        location.href = $el.attr('url');
                    }
                });
            }
        }
    };
});