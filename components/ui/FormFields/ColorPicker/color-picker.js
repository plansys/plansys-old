app.directive('colorPicker', function ($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }
            return function ($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.update = function () {
                    if (!!ctrl) {
                        $timeout(function () {
                            ctrl.$setViewValue($scope.color.toLowerCase());
                        }, 0);
                    }
                };

                // make sure colorpicker is loaded
                var cpi = setInterval(function () {
                    if (typeof $el.find('.colorpicker').colorpicker == "function") {
                        clearInterval(cpi);
                        $el.find('.colorpicker').colorpicker().on('changeColor', function (ev) {
                            ctrl.$setViewValue(ev.color.toHex());
                        });
                    }
                }, 100);

                // set default color
                $scope.color = $el.find("data[name=value]").html().trim();

                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.color = ngModelValue;
                        }
                    }, 0);
                }
            }
        }
    };
});