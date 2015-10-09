app.directive('psDataSource', function ($timeout, $http, $q) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);

                $scope.params = JSON.parse($el.find("data[name=params]").text());
                $scope.paramsGet = JSON.parse($el.find("data[name=params_get]").text());
                $scope.sqlParams = JSON.parse($el.find("data[name=params_default]").text());
                $scope.totalItems = $el.find("data[name=total_item]").text();
                $scope.name = $el.find("data[name=name]:eq(0)").text().trim();
                $scope.class = $el.find("data[name=class_alias]").text().trim();
                $scope.postData = $el.find("data[name=post_data]").text().trim();
                $scope.primaryKey = $el.find("data[name=primary_key]:eq(0)").text().trim();
                $scope.relationTo = $el.find("data[name=relation_to]").text().trim();
                $scope.insertData = [];
                $scope.updateData = [];
                $scope.httpRequest = false;
                $scope.loading = false;
                $scope.deleteData = JSON.parse($el.find("data[name=delete_data]").text());
                $scope.deleteData = $scope.deleteData || [];
                $scope.untrackColumns = [];

                if (!$scope.primaryKey) {
                    $scope.primaryKey = 'id';
                }

                $scope.resetData = function () {
                    $scope.deleteData.length = 0;
                    $scope.updateData.length = 0;
                    $scope.insertData.length = 0;
                }

                $scope.resetParam = function (key, name) {
                    if (typeof key == "undefined") {
                        for (i in $scope.sqlParams) {
                            delete $scope.sqlParams[i];
                        }
                    } else {
                        if (typeof $scope.sqlParams[name] != "undefined") {
                            delete $scope.sqlParams[name][key];
                        }
                    }
                }

                $scope.isRowEmpty = function (row, except) {
                    except = except || [];
                    for (i in row) {
                        if (row[i] != "" && $scope.untrackColumns.indexOf(i) < 0 && except.indexOf(i) < 0) {
                            return false;
                        }
                    }
                    return true;
                }

                $scope.updateParam = function (key, value, name) {

                    if (typeof name === "undefined") {
                        $scope.sqlParams[key] = value;
                        return true;
                    }

                    if (typeof $scope.sqlParams[name] == "undefined") {
                        $scope.sqlParams[name] = {};
                    }

                    if (typeof $scope.sqlParams[name] == "string" && key && value) {
                        $scope.sqlParams[name] = {};
                    }

                    $scope.sqlParams[name][key] = value;
                }

                $scope.setDebug = function (debug) {
                    if (typeof debug == "undefined") {
                        $scope.debugHTML = "";
                        return true;
                    }
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

                $scope.afterQueryInternal = {};
                $scope.beforeQueryInternal = {};
                $scope.beforeQuery = null;
                $scope.afterQuery = null;
                $scope.shouldCount = true;
                $scope.lastQueryFrom = "";

                $scope.queryWithoutCount = function (f) {
                    $scope.shouldCount = false;
                    $scope.query(f);
                }

                $scope.prepareParams = function () {
                    var params = $.extend({}, $scope.sqlParams);
                    for (i in $scope.params) {
                        if (i[0] == ':' && $scope.params[i]) {
                            if ($scope.params[i].substr(0, 3) == 'js:') {
                                params[i] = $scope.sqlParams[i];
                            } else {
                                params[i] = $scope.params[i];
                            }
                        }
                    }
                    return params;
                }

                $scope.showError = function (data) {
                    if (typeof data == "string" && data.length > 10) {
                        var iframeDoc = $el.find("iframe")[0].contentWindow.document;
                        iframeDoc.open();
                        iframeDoc.write(data);
                        iframeDoc.close();
                        $el.find(".error").show();
                    }
                }

                $scope.query = function (f) {
                    var model = $scope.model || {};
                    var model_id = model[$scope.primaryKey] || null;

                    for (i in $scope.beforeQueryInternal) {
                        $scope.beforeQueryInternal[i]($scope);
                    }

                    var params = $scope.prepareParams();
                    $scope.loading = true;

                    if ($scope.httpRequest) {
                        $scope.httpRequest.resolve();
                    }
                    $scope.httpRequest = $q.defer();

                    var executeSuccess = function (data) {
                        if (typeof data == "string") {
                            $scope.showError(data);
                        } else {
                            $scope.original = angular.copy($scope.data);
                            $scope.isDataReloaded = true;
                            $scope.data.splice(0, $scope.data.length);
                            $scope.data = $scope.data.concat(data.data);
                            $scope.totalItems = data.count * 1;
                            $scope.setDebug(data.debug);
                            if (typeof f == "function") {
                                f(true, data);
                            }
                            $scope.loading = false;

                            for (i in $scope.afterQueryInternal) {
                                $scope.afterQueryInternal[i]($scope);
                            }

                            if ($scope.afterQuery != null) {
                                $scope.afterQuery($scope);
                            }
                        }
                    }

                    if ($scope.beforeQuery != null) {
                        var shouldContinue = $scope.beforeQuery($scope);

                        if (shouldContinue === false) {
                            executeSuccess($scope.data);
                            return false;
                        }
                    }

                    $http.post(Yii.app.createUrl('/formfield/DataSource.query', $scope.paramsGet), {
                        model_id: model_id,
                        name: $scope.name,
                        class: $scope.class,
                        params: params,
                        modelParams: $scope.model,
                        lc: $scope.shouldCount ? 0 : $scope.totalItems
                    }, {
                        timeout: $scope.httpRequest.promise
                    })
                        .success(executeSuccess)
                        .error(function (data) {
                            if (typeof f == "function") {
                                f(false, data);
                            }
                            $scope.showError(data);
                        });
                    $scope.shouldCount = true;
                }

                var jsParamExist = false;
                angular.forEach($scope.params, function (p, i) {
                    if (p.indexOf('js:') === 0) {
                        var value = parent.$eval(p.replace('js:', ''));
                        var watch = parent.$eval('"' + p.replace('js:', '') + '"');
                        var key = i;
                        parent.$watchCollection(watch, function (newv, oldv) {
                            if (newv != oldv) {
                                $scope.updateParam(key, newv);
                                $scope.query();
                            }
                        });

                        $scope.updateParam(i, value)
                        jsParamExist = true;
                    }
                });

                if (jsParamExist) {
                    $scope.query();
                    $scope.data = [];
                } else {
                    $scope.data = JSON.parse($el.find("data[name=data]").text());
                }

                $scope.trackChanges = true;
                $scope.resetOriginal = function() {
                    $scope.original = angular.copy($scope.data);
                }

                if ($scope.postData == 'Yes') {
                    $scope.resetOriginal();

                    $scope.$watch('data', function (newval, oldval) {
                        if (typeof $scope.data == "undefined") {
                            $scope.data = [];
                        }
                        if (newval !== oldval && $scope.trackChanges) {
                            
                        }
                    }, true);
                }

                parent[$scope.name] = $scope;
            }

        }
    };
});
