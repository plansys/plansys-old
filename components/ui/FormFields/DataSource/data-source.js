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
                $scope.primaryKey = JSON.parse($el.find("data[name=primary_key]:eq(0)").text().trim());
                $scope.relationTo = $el.find("data[name=relation_to]").text().trim();
                $scope.options = JSON.parse($el.find("data[name=options]:eq(0)").text().trim());
                $scope.insertData = [];
                $scope.updateData = [];
                $scope.originalHash = {};
                $scope.deleteData = JSON.parse($el.find("data[name=delete_data]").text()) || [];
                $scope.httpRequest = false;
                $scope.loading = false;
                $scope.untrackColumns = [];
                $scope.dataFilterName = '';
                
                if (!!$scope.options['primaryKey']) {
                    $scope.primaryKey = $scope.options['primaryKey'];
                }
                
                if (!$scope.primaryKey) {
                    $scope.primaryKey = 'id';
                }
                
                $scope.enableTrackChanges = function (from) {
                    // console.log('DS WATCH [✓] from:', from);
                    $scope.trackChanges = true;
                }

                $scope.disableTrackChanges = function (from) {
                    // console.log('DS WATCH [✗] from:', from);
                    $scope.trackChanges = false;
                }

                $scope.resetData = function () {
                    $scope.deleteData.splice(0, $scope.deleteData.length);
                    $scope.updateData.splice(0, $scope.updateData.length);
                    $scope.insertData.splice(0, $scope.insertData.length);
                }
                
                $scope.reset = function() {
                    $scope.data.splice(0, $scope.data.length);
                    $scope.resetData();
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
                
                $scope.resetPage = function() {
                    $scope.resetPageSetting();
                    window.location.reload();
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
                    if (!$scope.sqlParams) {
                        $scope.sqlParams = {};
                    }
                    
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
                            if (!$scope.data || $scope.data === null) {
                                $scope.data = [];
                            }
                            if (!$scope.data.splice) {
                                alert($scope.name + " data is not an array!");
                            }
                            
                            //callback
                            if (typeof f == "function") {
                                f(true, data);
                            }
                            
                            $scope.data.splice(0, $scope.data.length);
                            $scope.data = $scope.data.concat(data.data);
                            $scope.totalItems = data.count * 1;
                            $scope.setDebug(data.debug);
                            $scope.loading = false;
                            

                            // Retain editing result between paging/sorting query
                            if ($scope.updateData.length > 0 ||
                                $scope.insertData.length > 0 ||
                                $scope.deleteData.length > 0) {

                                var pk = $scope.primaryKey,
                                    diffHash = {};

                                for (i in $scope.data) {
                                    diffHash[$scope.data[i][pk]] = $scope.data[i];
                                }

                                if ($scope.insertData.length > 0) {
                                    for (i in $scope.insertData) {
                                        var item = $scope.insertData[i];
                                        if (typeof diffHash[item[pk]] != "undefined") {
                                            // if inserted data is already available in current state
                                            // then the data should not be inserted, but rather updated
                                            $scope.updateData.push($scope.insertData.splice(i, 1));
                                        } else {
                                            $scope.data.push($scope.insertData[i]);
                                        }
                                    }
                                }

                                if ($scope.updateData.length > 0) {
                                    for (i in $scope.updateData) {
                                        var item = $scope.updateData[i];
                                        if (typeof diffHash[item[pk]] != "undefined") {
                                            $.extend(true, diffHash[item[pk]], item);
                                        } else {
                                            // if updated data is not available in current state
                                            // may be it is available on previous state, so do not change anything
                                            // because it will be sent anyway
                                        }
                                    }
                                }

                                if ($scope.deleteData.length > 0) {
                                    for (i in $scope.deleteData) {
                                        var item = $scope.deleteData[i];
                                        if (typeof diffHash[item[pk]] != "undefined") {
                                            diffHash[item[pk]].$rowState = 'remove';
                                        }
                                    }
                                }
                            }

                            // execute afterQueryInternal
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
                        df: $scope.dataFilterName,
                        lc: $scope.shouldCount ? 0 : $scope.totalItems
                    }, { timeout: $scope.httpRequest.promise })
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
                if (!!$scope.params) {
                    angular.forEach($scope.params, function (p, i) {
                        if (p != null && p.indexOf('js:') === 0) {
                            var value = parent.$eval(p.replace('js:', ''));
                            var watch = parent.$eval('"' + p.replace('js:', '') + '"');
                            var key = i;
                            parent.$watch(watch, function (newv, oldv) {
                                if (newv !== oldv) {
                                    $scope.updateParam(key, newv);
                                    
                                    $scope.disableTrackChanges("DataSource:paramChangesBeforeQuery");
                                    $scope.afterQueryInternal['params-' + watch] = function () {
                                        $scope.resetOriginal();
                                        $scope.enableTrackChanges("DataSource:paramChangesAfterQuery");
                                        delete $scope.afterQueryInternal['params-' + watch];
                                    }
                                    
                                    $scope.query();
                                }
                            },true);
    
                            $scope.updateParam(i, value)
                            jsParamExist = true;
                        }
                    });
                }

                
                $scope.enableTrackChanges("DataSource:init");
                $scope.resetOriginal = function () {
                    $scope.original = angular.copy($scope.data);
                }

                if (jsParamExist) {
                    $scope.afterQueryInternal['params-init'] = function () {
                        $scope.resetOriginal();
                        
                        $scope.enableTrackChanges("DataSource:initParamAfterQuery");
                        delete $scope.afterQueryInternal['params-init'];
                    }
                    $scope.query();
                } else {
                    var relationAvailable = ($scope.options.watchModel == "true" 
                                            && $scope.postData 
                                            && $scope.relationTo != "currentModel" 
                                            && !!$scope.model[$scope.relationTo]);
                    
                    if (relationAvailable) {
                        $scope.data = $scope.model[$scope.relationTo];
                    } else {
                        $scope.data = JSON.parse($el.find("data[name=data]:eq(0)").text());
                    }
                }
                
                for(i in $scope.data) {
                    if (!!$scope.data[i] && !!$scope.data[i].$rowState) {
                        if ($scope.data[i].$rowState == 'insert') $scope.insertData.push($scope.data[i]);
                        if ($scope.data[i].$rowState == 'edit') $scope.updateData.push($scope.data[i]);
                    } 
                }

                var diff = function (oldArray, newArray) {
                    var i, oldHash = {}, newHash = {}, pk = $scope.primaryKey,
                        diff = {
                            insert: [],
                            update: [],
                            delete: []
                        };
                        

                    for (i in oldArray) {
                        if (!!oldArray[i].$type && oldArray[i].$type != 'r') {
                            continue;
                        }

                        oldHash[oldArray[i][pk]] = oldArray[i];
                    }

                    for (i in newArray) {
                        if (!!newArray[i].$type && newArray[i].$type != 'r') {
                            continue;
                        }

                        var item = newArray[i];
                        var pkIsEmpty = !item[pk];
                        if (!!item[pk] && !!item[pk].toString) {
                            pkIsEmpty = (item[pk].toString() === '');
                        }
                        
                        if (!pkIsEmpty) {
                            if (!!newHash[item[pk]]) {
                                alert("ERROR!!!\nPrimary Key Column ("+pk+"): Data is not UNIQUE!");
                            } else {
                                newHash[item[pk]] = item;
                            }
                        }
                        
                        if (item.$rowState == 'edit') {
                            if (angular.equals(oldHash[item[pk]], item)) {
                                delete item.$rowState;
                            }
                        }

                        //if pk is empty --OR-- pk is not in old hash
                        if (pkIsEmpty || typeof oldHash[item[pk]] === "undefined") {
                            // then it is new item
                            item.$rowState = 'insert';
                            diff.insert.push(item);
                        } else {
                            // if pk is NOT empty --AND-- pk is IN old hash
                            // then maybe it is updated?
                            if (!angular.equals(item, oldHash[item[pk]])) {
                                if (!$scope.originalHash[item[pk]]) {
                                    $scope.originalHash[item[pk]] = angular.copy(oldHash[item[pk]]);
                                    
                                    item.$rowState = 'edit';
                                    diff.update.push(item);
                                } else if (angular.equals(item, $scope.originalHash[item[pk]])) {
                                    // if current item is equal to original item, then it is not updated
                                    delete item.$rowState;
                                    for (var i in $scope.updateData) {
                                        if ($scope.updateData[i][pk] == item[pk]) {
                                            $scope.updateData.splice(i,1);
                                            break;
                                        }
                                    }
                                } else {
                                    item.$rowState = 'edit';
                                    diff.update.push(item);
                                }
                            }
                        }
                    }

                    //check for deleted items
                    for (i in oldHash) {
                        // if old item is NOT available in new array
                        if (typeof newHash[i] == "undefined") {
                            // then it is definitely deleted
                            diff.delete.push(oldHash[i]);
                        }
                    }
                    
                    return diff;
                };
                
                if ($scope.postData == 'Yes') {
                    $scope.resetOriginal();
                    $scope.$watch('data', function (newval, oldval) {
                        if (typeof $scope.data == "undefined") {
                            $scope.data = [];
                        }
                        if (angular.equals($scope.original, newval)) {
                            for (var i in newval) {
                                delete newval[i].$rowState;
                            }  
                        } else if ($scope.trackChanges) {
                            var df = diff($scope.original, newval);
                            // Generate UpdateData Hash (to enable faster primary key look up)
                            var updateHash = {};
                            for (i in $scope.updateData) {
                                updateHash[$scope.updateData[i][$scope.primaryKey]] = {
                                    data: $scope.updateData[i],
                                    idx: i
                                };
                            }
                            // Generate DeleteData Hash (to enable faster primary key look up)
                            var deleteHash = {};
                            for (i in $scope.deleteData) {
                                deleteHash[$scope.deleteData[i][$scope.primaryKey]] = {
                                    data: $scope.deleteData[i],
                                    idx: i
                                };
                            }
                            
                            // Handle Insert Data
                            for (i in df.insert) {
                                if ($scope.insertData.indexOf(df.insert[i]) < 0) {
                                    $scope.insertData.push(df.insert[i]);
                                }
                            }

                            // Handle Update Data
                            for (i in df.update) {
                                if (!updateHash[df.update[i][$scope.primaryKey]]) {
                                    // if data is not in updateData, then add it to updateData
                                    $scope.updateData.push(df.update[i]);

                                    // if data is already marked as deleted, then mark it as updated
                                    // by removing it from deleteData array
                                    if (!!deleteHash[df.update[i][$scope.primaryKey]]) {
                                        $scope.deleteData.splice(deleteHash[df.update[i][$scope.primaryKey]].idx, 1);
                                    }
                                } else {
                                    // if data is already in updateData, then update it
                                    var updateItem = updateHash[df.update[i][$scope.primaryKey]];
                                    $.extend(true, $scope.updateData[updateItem.idx], df.update[i]);
                                }
                            }

                            // Handle Delete Data
                            for (i in df.delete) {
                                if (!deleteHash[df.delete[i][$scope.primaryKey]]) {
                                    $scope.deleteData.push(df.delete[i]);

                                    for (j in $scope.updateData) {
                                        if ($scope.updateData[j][$scope.primaryKey] == df.delete[i][$scope.primaryKey]) {
                                            $scope.updateData.splice(j, 1);
                                        }
                                    }
                                }
                            }
                        }
                        
                    },true);
                }

                parent[$scope.name] = $scope;
            }

        }
    };
});
