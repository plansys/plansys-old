app.directive('textField', function ($timeout, $http) {
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
                    if (typeof ctrl != 'undefined') {
                        $timeout(function () {
                            ctrl.$setViewValue($scope.value);
                        }, 0);
                    }
                };

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function () {
                        if ($scope.inEditor && !$scope.$parent.fieldMatch($scope))
                            return;

                        if (typeof ctrl.$viewValue != "undefined") {
                            $scope.value = ctrl.$viewValue;
                            $scope.update();
                        }
                    };
                }

                // set default value
                var keytimeout = null;
                $scope.name = $el.find("data[name=name]").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.relModelClass = $el.find("data[name=rel_model_class]").html();
                $scope.autocomplete = $el.find("data[name=autocomplete]").html();
                $scope.params = JSON.parse($el.find("data[name=params]").text());
                $scope.paramValue = {};
                $scope.list = [];
                $scope.showDropdown = false;

                for (i in $scope.params) {
                    var p = $scope.params[i];
                    if (p.indexOf('js:') === 0) {
                        var value = $scope.$parent.$eval(p.replace('js:', ''));
                        var key = i;

                        $scope.$parent.$watch(p.replace('js:', ''), function (newv, oldv) {
                            if (newv != oldv) {
                                for (i in $scope.params) {
                                    var x = $scope.params[i];
                                    if (x == p) {
                                        $scope.paramValue[i] = newv;
                                    }
                                }
                                $scope.doSearch();
                            }
                        }, true);

                        $scope.paramValue[key] = value;
                        $scope.doSearch();
                    }
                }

                $scope.doSearch = function () {
                    var val = $scope.value;
                    $http.post(Yii.app.createUrl('formfield/RelationField.search'), {
                        's': val,
                        'm': $scope.modelClass,
                        'f': $scope.name,
                        'p': $scope.paramValue
                    }).success(function (data) {
                        $scope.list = data;
                        $scope.showDropdown = true;
                        $timeout(function() {
                            $el.find("input[type=text]").focus();
                        },10);
                    });
                }
                $scope.choose = function () {
                    $scope.showDropdown = false;
                }

                $el.find("input[type=text]").keydown(function () {
                    clearTimeout(keytimeout);
                    keytimeout = setTimeout(function () {
                        if ($scope.autocomplete != '') {
                            switch ($scope.autocomplete) {
                                case "rel":
                                    $scope.doSearch();
                                    break
                                case "php":
                                    console.log($(this).val());
                                    break;
                            }
                        }
                    }, 50);
                });


                // if ngModel is present, use that instead of value from php
                if (attrs.ngModel) {
                    $timeout(function () {
                        var ngModelValue = $scope.$eval(attrs.ngModel);
                        if (typeof ngModelValue != "undefined") {
                            $scope.value = ngModelValue;
                        }
                    }, 0);
                }
            }
        }
    };
});