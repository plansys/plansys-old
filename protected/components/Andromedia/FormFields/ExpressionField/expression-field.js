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
                    if (typeof $scope.$parent.active[$scope.fieldName] == "undefined")
                        return;

                    execute_action = typeof execute_action != "undefined" ? execute_action : true;

                    $scope.$parent.active[$scope.fieldName] = $scope.value;
                    if (typeof ctrl != 'undefined') {
                        $timeout(function() {
                            ctrl.$setViewValue($scope.value);
                            if (execute_action) {
                                $scope.$parent.$eval($scope.validAction, {result: result});
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

                    if ($scope.lang == "php" || $scope.lang == "sql") {
                        // validate php / sql
                        $http.post(Yii.app.createUrl('formfield/ExpressionField.validate'), {
                            expr: $scope.value,
                            lang: $scope.lang
                        }).success(function(data) {
                            $scope.validating = false;
                            if (data == '--invalid--') {
                                $scope.valid = false;
                            } else {
                                $scope.valid = true;
                                $scope.applyValue(data, execute_action);
                            }
                        });
                    } else {
                        // validate js
                        $scope.applyValue([], execute_action);
                    }
                };
                $scope.$watch('value', function(current, old) {
                    if (current == old || current == '') {
                        $scope.validating = false;
                        $scope.valid = true;
                    } else if ($scope.lang == "php" || $scope.lang == "sql") {
                        $scope.validating = true;
                    }
                }, true);
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
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

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
                $scope.validAction = $el.find("data[name='field_valid_action']").text().trim();
                $scope.valid = true;
                $scope.isFocus = false;
                $scope.validating = false;
                $scope.inEditor = typeof $scope.$parent.inEditor != "undefined";
                $scope.modelClass = $el.find("data[name=model_class]").html();
            }
        }
    };
});