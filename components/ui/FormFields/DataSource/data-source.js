app.directive('psDataSource', function($timeout, $http) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {
            return function($scope, $el, attrs, ctrl) {
                $scope.data = JSON.parse($el.find("data[name=data]").text());

                $scope.setDebug = function(debug) {
                    $scope.debug = debug;
                    if ($scope.debug.sql) {
                        $scope.debug.sql = $scope.debug.sql.replace(/\r/g, '').replace(/\n/g, '');
                    }
                    if ($scope.debug.countSQL) {
                        $scope.debug.countSQL = $scope.debug.countSQL.replace(/\r/g, '').replace(/\n/g, '');
                    }
                    if ($scope.debug.function) {
                        $scope.debug.function = $scope.debug.function.replace(/\r/g, '').replace(/\n/g, '');
                    }
                    if ($scope.debug.countFunction) {
                        $scope.debug.countFunction = $scope.debug.countFunction.replace(/\r/g, '').replace(/\n/g, '');
                    }
                    $scope.debugHTML = JSON.stringify($scope.debug, undefined, 2);
                };

                if ($el.find("data[name=debug]").length > 0) {
                    $scope.setDebug(JSON.parse($el.find("data[name=debug]").text()));
                } else {
                    $scope.setDebug({});
                }

                $scope.params = JSON.parse($el.find("data[name=params]").text());
                $scope.paramsGet = JSON.parse($el.find("data[name=params_get]").text());
                $scope.sqlParams = JSON.parse($el.find("data[name=params_default]").text());
                $scope.totalItems = $el.find("data[name=total_item]").text();
                $scope.name = $el.find("data[name=name]").text().trim();
                $scope.class = $el.find("data[name=class_alias]").text().trim();

                $scope.query = function(f) {

                    $http.post(Yii.app.createUrl('/formfield/DataSource.query', $scope.paramsGet), {
                        name: $scope.name,
                        class: $scope.class,
                        params: angular.extend(angular.copy($scope.params), $scope.sqlParams)
                    }).success(function(data) {
                        $timeout(function() {
                            $scope.data = data.data;
                            $scope.totalItems = data.count * 1;
                            $scope.setDebug(data.debug);
                            if (typeof f == "function") {
                                f(true, data);
                            }
                            
                        }, 0);
                    }).error(function(data) {
                        if (typeof f == "function") {
                            f(false, data);
                        }
                    });
                }

                $scope.updateParam = function(key, value, name) {
                    if (typeof $scope.sqlParams[name] == "undefined") {
                        $scope.sqlParams[name] = {};
                    }

                    $scope.sqlParams[name][key] = value;
                }

                $scope.resetParam = function(key, name) {
                    if (typeof key == "undefined") {
                        for (i in $scope.sqlParams) {
                            delete $scope.sqlParams[i];
                        }
                    } else {
                        delete $scope.sqlParams[name][key];
                    }
                }

                $scope.$parent[$scope.name] = $scope;
            }

        }
    };
});