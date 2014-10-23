app.directive('expressionField', function($timeout, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function(element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function($scope, $el, attrs, ctrl) {

                // when ng-model is changed from inside directive
                $scope.applyValue = function(result, execute_action) {
                    execute_action = typeof execute_action != "undefined" ? execute_action : true;

                    if (typeof ctrl != 'undefined') {
                        $timeout(function() {
                            ctrl.$setViewValue($scope.value);
                            if (typeof attrs.ngChange == "undefined") {
                                $scope.$parent.save();
                            }
                        }, 0);
                    }
                }

                $scope.validate = function(execute_action) {

                    execute_action = typeof execute_action != "undefined" ? execute_action : true;

                    if ($scope.value == "") {
                        // empty expression is always valid
                        $scope.applyValue([], execute_action);
                        $scope.valid = true;
                        return;
                    }
                    
                    $scope.applyValue([], execute_action);
                };
                $scope.forceFocus = function() {
                    $scope.isFocus = 1;
                    $timeout(function() {
                        $el.find("textarea").focus();
                    }, 0);
                }
                $scope.focus = function() {
                    $scope.isFocus = true;
                };
                $scope.blur = function() {
                    $scope.isFocus = false;
                };

                // when ng-model is changed from outside directive  
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function() {

                        if (typeof ctrl.$viewValue != 'undefined') {
                            $scope.value = ctrl.$viewValue;
                            $scope.validate(false);
                        }
                    };
                }

                // set default value, executed when one formfield is selected
                $scope.value = $el.find("data[name='value']").text().trim();
                $scope.modelClass = $el.find("data[name='model_class']").text().trim();
                $scope.fieldName = $el.find("data[name='field_name']").text().trim();
                $scope.lang = $el.find("data[name='field_language']").text().trim();
                $scope.isFocus = false;
                $scope.validating = false;
                $scope.valid = false;
               
                $scope.modelClass = $el.find("data[name=model_class]").html();
            }
        }
    };
});