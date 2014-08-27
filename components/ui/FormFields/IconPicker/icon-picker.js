app.directive('iconPicker', function($timeout) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {
                // when ng-model is changed from inside directive
                $scope.is_open = false;
                $scope.search = "";
                $scope.open = function open() {
                    $scope.is_open = true;

                    $timeout(function() {
                        $parentDiv = $el.find(".popover-content");
                        $innerListItem = $el.find(".btn.btn-primary");

                        if ($innerListItem.length > 0) {
                            $parentDiv.scrollTop($parentDiv.scrollTop() + $innerListItem.position().top
                                    - $parentDiv.height() / 2 + $innerListItem.height() / 2);
                        }
                    }, 0);
                }

                $scope.iconClass = function(value) {
                    if (value == $scope.value) {
                        return 'btn-primary';
                    } else {
                        return'btn-default';
                    }
                }

                $scope.select = function(value) {
                    $scope.icon = $('.btn-popover-item[value="' + value + '"]').html();
                    $scope.value = value;
                    $scope.is_open = false;

                    if (typeof ctrl != 'undefined') {
                        $timeout(function() {
                            ctrl.$setViewValue($scope.value);
                        }, 0);
                    }
                }

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    if (!$scope.inEditor) {
                        //watch ng-model for change
                        $scope.$watch(attrs.ngModel, function() {
                            if (attrs.ngChange) {
                                $scope.$parent.$eval(attrs.ngChange);
                            }
                        });
                    }

                    ctrl.$render = function() {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (ctrl.$viewValue != "undefined") {
                            $scope.select(ctrl.$viewValue);
                        }
                    };
                }

                // set default value
                $scope.value = $el.find("data[name=value]").html();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";

                //if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function() {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.select(ngModelValue);
                        }
                    }, 0);
                }
            }
        }
    };
});