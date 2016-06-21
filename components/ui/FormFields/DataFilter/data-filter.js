app.directive('psDataFilter', function ($timeout, dateFilter, $http, $localStorage) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            //hide filter criteria when mouse is clicked outside
            $(document).mouseup(function (e) {
                var container = $(".filter-criteria");

                if (!container.is(e.target) && container.parent().has(e.target).length === 0) {
                    container.hide().removeClass('open');
                    $(".filter-criteria-button").removeClass('active');
                }
            });


            return function ($scope, $el, attrs, ctrl) {
                var parent = $scope.getParent($scope);

                /************* All Filter **************/
                $scope.toggleFilterCriteria = function (e) {
                    var parent = $(e.target).parents('.btn-group');
                    var btn = parent.find('.filter-criteria-button');
                    if (parent.find("> .filter-criteria").hasClass('open')) {
                        parent.find("> .filter-criteria").removeClass('open');
                        parent.find("> .filter-criteria").hide();
                        btn.removeClass('active');
                    } else {
                        // hide another filter criteria
                        $(".filter-criteria").hide().removeClass('open');
                        $(".filter-criteria-button").removeClass('active');
                        btn.addClass('active');

                        // show current filter criteria
                        parent.find("> .filter-criteria").addClass('open');
                        parent.find("> .filter-criteria").show();
                        parent.find('.focused').focus();

                        // reposition filter criteria
                        var container_width = parent.parents('.container-full').width();
                        var parent_left = parent.find("> .filter-criteria").offset().left;
                        var parent_width = parent.find("> .filter-criteria").width();

                        if (parent_left + parent_width > container_width) {
                            var margin_left = parent_left + parent_width - container_width + 50;
                            parent.find("> .filter-criteria").css('margin-left', "-" + margin_left + "px");
                            $scope.toggleFilterCriteria(e);
                            $scope.toggleFilterCriteria(e);
                        }
                    }
                }

                $scope.savePageSetting = function () {
                    $scope.pageSetting.dataFilters = $scope.pageSetting.dataFilters || {};
                    $scope.pageSetting.dataFilters[$scope.name] = $scope.filters;
                }

                $scope.isCached = function () {
                    if (!$scope.pageSetting) return false;

                    return !!$scope.pageSetting.dataFilters && !!$scope.pageSetting.dataFilters[$scope.name];
                }

                $scope.loadPageSetting = function () {
                    if (!!$scope.pageSetting.dataFilters && !!$scope.pageSetting.dataFilters[$scope.name]) {

                        $scope.oldFilters = angular.copy($scope.filters);
                        $scope.filters.length = 0;
                        $scope.pageSetting.dataFilters[$scope.name].forEach(function (filter, k) {
                            switch (filter.filterType) {
                                case "dropdown":
                                case "relation":
                                case "checkbox":
                                    filter.list = [];
                                    filter.operatorDropdownOpen = false;
                                    filter.initRel = false;

                                    break;
                                case "date":
                                    if (typeof filter.value == "string") {
                                        filter.from = new Date(strtotime(filter.value) * 1000);
                                    } else {
                                        if (!filter.value.from) {
                                            filter.from = $scope.oldFilters[k].from;
                                        } else {
                                            filter.from = new Date(strtotime(filter.value.from) * 1000);
                                        }
                                    }

                                    if (!!filter.value && !!filter.value.to) {
                                        filter.to = new Date(strtotime(filter.value.to) * 1000);

                                        if (!filter.to) {
                                            filter.to = $scope.oldFilters[k].to;
                                        }
                                    }

                                    if (['Between', 'Not Between'].indexOf(filter.operator) >= 0) {
                                        if (!filter.value.from || !filter.value.to) {
                                            $scope.resetFilter(filter);
                                        }
                                    }

                                    break;
                            }

                            $scope.filters.push(filter);
                        });

                    }
                }

                
                $scope.setFirst = function(filter) {
                    if (!isNaN(filter)) {
                        filter = $scope.filters[filter];
                    }
                    if (filter.filterType == 'relation') {
                        filter.loading = true;
                        filter.list = [];
                        filter.initRel = false;
                        filter.value = "";
                        $scope.relationNext(false, filter, function(data) {
                            if (data.count > 0) {
                                filter.value = data.list[0].key;
                                $scope.updateFilter(filter);
                            } else {
                                filter.value = "";
                                filter.valueText = "";
                            }
                        });
                    }
                }
                
                $scope.resetFilter = function (filter) {
                    
                    if (!isNaN(filter)) {
                        filter = $scope.filters[filter];
                    }
                    
                    filter.value = '';
                    if (filter.filterType == 'date') {
                        filter.from = '';
                        filter.to = '';
                    }

                    if ($scope.operators[filter.filterType]) {
                        filter.operator = $scope.operators[filter.filterType][0];
                    } else {
                        filter.operator = "";
                    }

                    if (filter.checked) {
                        filter.checked.length = 0;
                    }

                    $scope.changeValueText(filter);

                    if (filter.filterType == 'relation') {
                        filter.loading = true;
                        filter.list = [];
                        filter.initRel = false;
                    }
                    
                    $scope.datasources.map(function (dataSourceName) {
                        var ds = parent[dataSourceName];
                        var dsParamName = "";
                        if (filter.isCustom === "Yes") {
                            dsParamName = filter.name;
                        } else {
                            dsParamName = 'where';
                        }

                        if (dsParamName != "" && (!filter.isCustom || filter.isCustom === "No")) {
                            var prepared = $scope.prepareDSParams(filter);
                            ds.resetParam(prepared.name, dsParamName);

                            if (typeof $scope.beforeQuery == 'function') {
                                $scope.beforeQuery(ds);
                            }

                            ds.afterQueryInternal[$scope.renderID] = function () {
                                if (ds.params.paging && $scope[ds.params.paging]
                                    && $scope[ds.params.paging].gridOptions) {

                                    var paging = $scope[ds.params.paging].gridOptions.pagingOptions;
                                    if (!paging)
                                        return;

                                    if (paging.currentPage * paging.pageSize > ds.totalItems) {
                                        paging.currentPage = Math.floor(ds.totalItems / paging.pageSize);
                                    } else if (paging.currentPage == 0 && ds.totalItems > 0) {
                                        paging.currentPage = 1;
                                    }
                                }
                                ds.enableTrackChanges('DataFilter:ResetFilter');
                            }
                            
                            ds.disableTrackChanges('DataFilter:reset');
                            ds.lastQueryFrom = "DataFilter";
                            ds.query(function () {
                                delete ds.afterQueryInternal[$scope.renderID];

                                if (typeof $scope.afterQuery == 'function') {
                                    $scope.afterQuery(ds);
                                }
                            });
                        }
                    });
                }

                $scope.initFilters = function (filters) {
                    for (i in filters) {
                        var f = filters[i];
                        f.value = '';
                        f.show = (i > 5 ? false : true);
                        f.valueText = 'All';
                        f.operatorDropdownOpen = false;
                        if ($scope.operators[f.filterType]) {
                            f.operator = $scope.operators[f.filterType][0];
                        } else {
                            f.operator = "";
                        }

                        if (!f.resetable) {
                            f.resetable = 'Yes';
                        }

                        if (['list', 'check', 'relation'].indexOf(f.filterType) >= 0) {
                            f.list = $scope.renderFormList(f.list);
                            f.searchable = (f.list.length > 6 ? true : false);
                            f.search = '';

                            if (f.filterType == 'check') {
                                f.checked = [];
                                f.checkedLength = f.checked.length;
                            }
                            
                        }
                    }
                    return filters;
                }

                $scope.prepareDSParams = function (filter) {
                    var prepared = angular.copy(filter);
                    prepared.name = filter.name;
                    prepared.value = filter.value;

                    switch (filter.filterType) {
                        case 'relation':
                            if (filter.relIncludeEmpty == 'Yes') {
                                prepared.operator = 'empty';
                                prepared.value = filter.relEmptyValue;
                            } else {
                                prepared.operator = '';
                            }
                            break;
                        case 'check':
                            if (!!filter.queryOperator && filter.queryOperator == "in") {
                                prepared.operator = 'in';
                            }
                            break;
                    }
                    return prepared;
                }

                $scope.beforeQuery = function (ds) {
                };
                $scope.afterQuery = function (ds) {
                };
                $scope.updateFilter = function (filter, e, shouldExec) {
                    $scope.changeValueText(filter);
                    
                    shouldExec = typeof shouldExec == "undefined" ? true : shouldExec;
                    if (typeof e != "undefined" && e != null &&
                        ['list', 'check', 'relation'].indexOf(filter.filterType) < 0) {
                        $scope.toggleFilterCriteria(e);
                    }

                    if (filter.filterType == "date" && ['Between', 'Not Between'].indexOf(filter.operator) >= 0) {
                        if (!filter.value.from || !filter.value.to) {
                            shouldExec = false;
                        }
                    }
                    
                    $scope.datasources.map(function (dataSourceName) {
                        var ds = parent[dataSourceName];
                        if (!ds) {
                            return false;
                        }

                        var dsParamName = "";

                        if (filter.isCustom === "Yes") {
                            dsParamName = filter.name;
                        } else {
                            dsParamName = 'where';
                        }

                        if (dsParamName != "") {
                            if (!!filter.value && filter.value != "") {
                                var prepared = $scope.prepareDSParams(filter);
                                ds.updateParam(prepared.name, {
                                    value: prepared.value,
                                    operator: prepared.operator,
                                    type: prepared.filterType
                                }, dsParamName);
                            } else {
                                ds.resetParam(filter.name, dsParamName);
                            }

                            if (shouldExec) {
                                if (typeof $scope.beforeQuery == 'function') {
                                    $scope.beforeQuery(ds);
                                }

                                ds.afterQueryInternal[$scope.renderID] = function () {
                                    if (ds.params.paging && $scope[ds.params.paging]
                                        && $scope[ds.params.paging].gridOptions) {
                                        var paging = $scope[ds.params.paging].gridOptions.pagingOptions;
                                        if (paging.currentPage * paging.pageSize > ds.totalItems) {
                                            paging.currentPage = Math.floor(ds.totalItems / paging.pageSize);
                                        } else if (paging.currentPage == 0 && ds.totalItems > 0) {
                                            paging.currentPage = 1;
                                        }
                                    }
                                    ds.enableTrackChanges('DataFilter:UpdateFilter');
                                }
                                
                                ds.disableTrackChanges('DataFilter:UpdateFilter');
                                ds.lastQueryFrom = "DataFilter";
                                ds.query(function () {
                                    delete ds.afterQueryInternal[$scope.renderID];

                                    if (typeof $scope.afterQuery == 'function') {
                                        $scope.afterQuery(ds);
                                    }
                                    if (!!filter.options && !!filter.options['ng-change']) {
                                        $scope.$eval(filter.options['ng-change']);
                                    }
                                });
                            }
                        }
                    });

                    if (!!shouldExec) {
                        $scope.savePageSetting();
                    }
                }

                /************** Filter Dropdown ***************/
                $scope.listFound = function (input, filter) {
                    if (typeof filter.search == "undefined") {
                        return '';
                    }
                    return input.toLowerCase().indexOf(filter.search.toLowerCase()) > -1;
                }

                $scope.isObject = function (input) {
                    return angular.isObject(input);
                }
                $scope.dropdownSearchKeypress = function (e) {
                    if (e.which == 13) {
                        $scope.relationNext(e, $scope.filter);
                        e.preventDefault();
                    }
                }

                $scope.listSearch = function (e, filter) {
                    if (!filter) return;

                    if (filter.filterType == "relation") {
                        $timeout(function () {
                            $scope.loading = true;

                            var params = {};
                            for (var i in filter.relParams) {
                                var p = filter.relParams[i];
                                if (p.indexOf('js:') === 0) {
                                    var value = $scope.$eval(p.replace('js:', ''));
                                    params[i] = value;
                                }
                            }

                            $http.post(Yii.app.createUrl('formfield/DataFilter.relnext'), {
                                's': filter.search,
                                'f': $scope.name,
                                'n': filter.name,
                                'm': $scope.modelClass,
                                'i': 0,
                                'p': params
                            }).success(function (data) {
                                $scope.loading = false;
                                filter.list.length = 0;

                                if (data.list && data.list.length && data.list.length > 0) {
                                    data.list.forEach(function (item) {
                                        filter.list.push(item);
                                    });

                                    if (data.count) {
                                        filter.count = data.count;
                                    }
                                }

                                if (!data.list || (data.list.length && data.list.length == 0)) {
                                    filter.count = 0;
                                }
                            });
                        });
                    }
                };

                $scope.relationInit = function (filter) {
                    $scope.loading = true;
                    $http.post(Yii.app.createUrl('formfield/DataFilter.relInit'), {
                        'v': filter.value,
                        'f': $scope.name,
                        'n': filter.name,
                        'm': $scope.modelClass
                    }).success(function (data) {
                        $scope.loading = false;
                        filter.list.push({
                            key: data[0].value,
                            value: data[0].label
                        });
                        filter.dropdownText = data[0].label;
                        filter.valueText = filter.dropdownText;
                    });

                    return false;
                }

                $scope.relationNext = function (e, filter, f) {
                    if (!!e) {
                        e.stopPropagation();
                        e.preventDefault();
                    }
                    
                    if (!filter) return;
                    $scope.loading = true;

                    var params = {};
                    for (var i in filter.relParams) {
                        var p = filter.relParams[i];
                        if (p.indexOf('js:') === 0) {
                            var value = $scope.$eval(p.replace('js:', ''));
                            params[i] = value;
                        }
                    }
                    $http.post(Yii.app.createUrl('formfield/DataFilter.relnext'), {
                        's': filter.search,
                        'f': $scope.name,
                        'n': filter.name,
                        'm': $scope.modelClass,
                        'i': filter.list.length,
                        'p': params
                    }).success(function (data) {
                        $scope.loading = false;
                        if (data.list && data.list.length && data.list.length > 0) {
                            data.list.forEach(function (item) {
                                filter.list.push(item);
                            });
                        }
                        if (data.count) {
                            filter.count = data.count;
                        }

                        if (filter.list.length > 5) {
                            filter.searchable = true;
                        }
                        
                        if (typeof f == "function") {
                            f(data);
                        }
                    });

                    return false;
                }

                $scope.toggleShowFilter = function (filter) {
                    filter.show = (filter.show ? false : true);
                    $scope.savePageSetting();
                }

                $scope.dropdownClick = function (filter, e) {
                    if (filter.searchable) {
                        $(e.target).parents("[dropdown]").find(".search-dropdown").focus();
                    }

                    var scroll = 0;
                    var active = $(e.target).parents("[dropdown]").find(".dropdown-menu li.hover");
                    if (active.length > 0) {
                        scroll = active.position().top + 50;
                    }
                    $(e.target).parents("[dropdown]").find(".dropdown-menu").scrollTop(scroll);

                    if (filter.filterType == "relation" && !filter.initRel) {
                        filter.initRel = true;
                        filter.count = 9999;
                        filter.list = [];
                        $scope.relationNext(e, filter);
                    }

                }

                $scope.generateUrl = function (url, type) {
                    var output = '';
                    if (typeof url == "string") {

                        var match = url.match(/{([^}]+)}/g);
                        for (i in match) {
                            var m = match[i];
                            m = m.substr(1, m.length - 2);
                            var result = "' + row.getProperty('" + m + "') + '";
                            if (m.indexOf('.') > 0) {
                                result = $scope.$eval(m);
                            }
                            url = url.replace('{' + m + '}', result);
                        }

                        if (url.match(/http*/ig)) {
                            output = url.replace(/\{/g, "'+ row.getProperty('").replace(/\}/g, "') +'");
                        } else if (url.trim() == '#') {
                            output = '#';
                        } else {
                            url = url.replace(/\?/ig, '&');
                            output = "Yii.app.createUrl('" + url + "')";
                        }

                        if (type == 'html') {
                            if (output != '#') {
                                output = '{{' + output + '}}';
                            }
                        }

                    }
                    return $scope.$eval(output);
                }

                $scope.updateDropdown = function (e, filter, value) {
                    filter.value = value.key;
                    filter.dropdownText = value.value;

                    if (filter.filterType == 'check') {
                        e.stopPropagation();
                        e.preventDefault();

                        var index = filter.checked.indexOf(filter.value);

                        if (index >= 0) {
                            filter.checked.splice(index, 1);
                        } else {
                            filter.checked.push(filter.value);
                        }

                        filter.value = filter.checked;
                    }

                    if (!!value.url) {
                        var url = value.url.substr(4);
                        location.href = $scope.generateUrl(url);
                        return false;
                    }

                    $scope.updateFilter(filter, e);
                }

                $scope.dropdownChecked = function (filter, item) {
                    if (filter.valueText == 'All') {
                        return false;
                    } else if (!!item && !!item.key && filter.checked.indexOf(item.key) >= 0) {
                        return true;
                    } else {
                        return false;
                    }
                }

                $scope.renderFormList = function (list) {
                    var newList = [];
                    for (key in list) {
                        if (angular.isObject(list[key])) {
                            if (!!list[key].key) {
                                newList.push({key: list[key].key, value: list[key].value});
                            } else {
                                var subItem = [];
                                var rawSub = list[key];
                                for (subkey in rawSub) {
                                    subItem.push({key: subkey, value: rawSub[subkey]});
                                }
                                newList.push({key: key, value: subItem});
                            }
                        } else {
                            if (list[key].indexOf('url:') == 0) {
                                newList.push({key: key, value: key, url: list[key]});
                            } else {
                                newList.push({key: key, value: list[key]});
                            }
                        }
                    }
                    return newList
                }

                /*************** Filter Text *************/
                $scope.focused = function (e) {
                    $(e.target).parents('.input-group').find('.focused').focus();
                }

                $scope.filterValueKeydown = function (filter, e) {
                    switch (e.which) {
                        case 13:
                            e.preventDefault();
                            e.stopPropagation();
                            $scope.updateFilter(filter, e);
                            break;
                    }
                }

                $scope.dateChangeOperator = function (f, o, e) {
                    $scope.changeOperator(f, o, e);
                    if (['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(f.operator) >= 0) {
                        $scope.updateFilter(f, e);
                    }
                }

                $scope.changeValueText = function (filter) {
                    var dateCondition = filter.filterType == "date" && ['Between', 'Not Between', 'More Than', 'Less Than'].indexOf(filter.operator) >= 0;
                    if (filter.operator == 'Is Empty') {
                        filter.valueText = 'Is Empty';
                        filter.value = '- Empty -';
                    } else if (filter.value == '' && (dateCondition || filter.filterType != "date")) {
                        filter.valueText = 'All';
                    } else {
                        switch (filter.filterType) {
                            case "list":
                            case "relation":
                                if (typeof filter.dropdownText == "undefined") {
                                    for (i in filter.list) {
                                        if (filter.value == filter.list[i].key) {
                                            filter.dropdownText = filter.list[i].value;
                                            break;
                                        }
                                    }

                                    if (!filter.dropdownText) {
                                        $scope.relationInit(filter);
                                    }
                                }

                                filter.valueText = filter.dropdownText;
                                break;
                            case "check":
                                if (filter.checked.length == filter.checkedLength) {
                                    filter.valueText = 'All';
                                } else {
                                    filter.valueText = filter.checked.join(", ");
                                }
                                break;
                            case "string":
                                filter.valueText = filter.operator + " [" + filter.value + "]";
                                break;
                            case "number":
                                filter.valueText = filter.operator + " " + filter.value;
                                break;
                            case "date":
                                var from = dateFilter(filter.from, 'dd/MM/yyyy');
                                if (typeof from == "undefined" || from == '01/01/1970') {
                                    from = "";
                                }

                                var to = dateFilter(filter.to, 'dd/MM/yyyy');
                                if (typeof to == "undefined") {
                                    to = "";
                                }
                                
                                switch (filter.operator) {
                                    case "Between":
                                        filter.valueText = filter.operator + " " + from + " - " + to;
                                        break;
                                    case "Not Between":
                                        filter.valueText = filter.operator + " " + from + " - " + to;
                                        break;
                                    case "Less Than":
                                        filter.valueText = filter.operator + " " + to;
                                        break;
                                    case "More Than":
                                        filter.valueText = filter.operator + " " + from;
                                        break;
                                    case "Daily":
                                        if (from == "") {
                                            filter.from = new Date();
                                        }

                                        if (typeof filter.from == 'string') {
                                            filter.from = new Date(filter.from);
                                        }

                                        from = dateFilter(filter.from, 'dd MMM yyyy');
                                        filter.value = dateFilter(filter.from, 'yyyy-MM-dd HH:mm:00');
                                        filter.valueText = from;
                                        break;
                                    case "Weekly":
                                        if (from == "") {
                                            filter.from = new Date();
                                        }
                                        var monthName = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                                        if (typeof filter.from == 'string') {
                                            filter.from = new Date(filter.from);
                                        }
                                        var curr = filter.from;
                                        var startWeekDay = curr.getDate() - curr.getDay();
                                        var first = new Date(curr);
                                        var last = new Date(curr);
                                        first.setDate(startWeekDay);
                                        last.setDate(startWeekDay + 6);

                                        Date.prototype.getWeekOfMonth = function (exact) {
                                            var month = this.getMonth()
                                                , year = this.getFullYear()
                                                , firstWeekday = new Date(year, month, 1).getDay()
                                                , lastDateOfMonth = new Date(year, month + 1, 0).getDate()
                                                , offsetDate = this.getDate() + firstWeekday - 1
                                                , index = 1 // start index at 0 or 1, your choice
                                                , weeksInMonth = index + Math.ceil((lastDateOfMonth + firstWeekday - 7) / 7)
                                                , week = index + Math.floor(offsetDate / 7)
                                                ;
                                            if (exact || week < 2 + index)
                                                return week;
                                            return week === weeksInMonth ? index + 5 : week;
                                        };

                                        var weekNum = first.getWeekOfMonth(true);
                                        var monthYear = monthName[first.getMonth()] + " " + (first.getYear() + 1900);
                                        if (weekNum == 5) {
                                            var test = new Date(last);
                                            test.setDate(test.getDate() + 3);
                                            if (test.getWeekOfMonth(true) == 2) {
                                                var nr = test.getMonth();
                                                var yr = 0;
                                                if (nr >= 12) {
                                                    nr = 0;
                                                    yr = 1;
                                                }
                                                weekNum = 1;
                                                monthYear = monthName[nr] + " " + (test.getYear() + yr + 1900);
                                            }
                                        }
                                        if (weekNum == 6) {
                                            var nr = first.getMonth() + 1;
                                            var yr = 0;
                                            if (nr >= 12) {
                                                nr = 0;
                                                yr = 1;
                                            }
                                            weekNum = 1;
                                            monthYear = monthName[nr] + " " + (first.getYear() + yr + 1900);
                                        }

                                        filter.value = {};
                                        filter.from = new Date(first);
                                        filter.to = new Date(last);
                                        filter.value.from = dateFilter(first, 'yyyy-MM-dd HH:mm:00');
                                        filter.value.to = dateFilter(last, 'yyyy-MM-dd HH:mm:00');

                                        filter.valueText = "Week " + weekNum + " (" + monthYear + ")";
                                        break;
                                    case "Monthly":

                                        if (from == "") {
                                            filter.from = new Date();
                                        }
                                        var monthName = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                                            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

                                        if (typeof filter.from == 'string') {
                                            filter.from = new Date(filter.from);
                                        }

                                        var curr = filter.from;

                                        if (filter.options && filter.options.monthSpan) {
                                            monthSpan = (filter.options.monthSpan || 1) * 1;
                                        } else {
                                            monthSpan = 1;
                                        }

                                        startingMonth = curr.getMonth() - (curr.getMonth() % monthSpan);
                                        first = new Date(curr.getFullYear(), startingMonth, 1);
                                        last = new Date(curr.getFullYear(), startingMonth + monthSpan, 0);

                                        filter.from = new Date(first);
                                        filter.to = new Date(last);
                                        filter.value = {};
                                        filter.value.from = dateFilter(first, 'yyyy-MM-dd HH:mm:00');
                                        filter.value.to = dateFilter(last, 'yyyy-MM-dd HH:mm:00');

                                        filter.valueText = monthName[filter.from.getMonth()] + " " + (filter.from.getYear() + 1900);
                                        if (monthSpan > 1) {
                                            filter.valueText += " - " + monthName[filter.to.getMonth()] + " " + (filter.to.getYear() + 1900);
                                        }
                                        break;
                                    case "Yearly":
                                        if (from == "") {
                                            filter.from = new Date();
                                        }

                                        var curr = filter.from;

                                        if (typeof curr != "object") {
                                            curr = new Date();
                                            curr.setFullYear(filter.from);
                                        }

                                        first = new Date(curr.getFullYear(), 0, 1);
                                        last = new Date(curr.getFullYear() + 1, 0, 0);

                                        filter.from = new Date(first);
                                        filter.value = {};
                                        filter.value.from = dateFilter(first, 'yyyy-MM-dd HH:mm:00');
                                        filter.value.to = dateFilter(last, 'yyyy-MM-dd HH:mm:00');

                                        filter.valueText = curr.getFullYear();
                                        break;
                                }
                        }
                    }
                }

                /************** Filter Date *************/
                $scope.changeOperator = function (filter, operator, e) {
                    filter.operator = operator;
                    filter.operatorDropdownOpen = false;
                    $scope.focused(e);
                    $scope.changeValueText(filter, filter.value);
                }

                $scope.getDate = function (col) {
                    var ret = "";
                    $scope.filters.forEach(function (item, idx) {
                        if (item.filterType != 'date' && item.name != "col")
                            return;

                        if (typeof $scope.filters[idx].valueText == "string" || typeof $scope.filters[idx].valueText == "number") {
                            ret = date("Y-m-d", strtotime($scope.filters[idx].value.from));
                            if (typeof $scope.filters[idx].value.from === 'undefined') {
                                ret = date("Y-m-d", strtotime($scope.filters[idx].value));
                            }
                        } else {
                            ret = "";
                        }
                    });

                    return ret;
                }

                $scope.datePrev = function (filter, e) {
                    if (['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) >= 0) {
                        switch (filter.operator) {
                            case 'Daily':
                                filter.from.setDate(filter.from.getDate() - 1);
                                $scope.updateFilter(filter);
                                break;
                            case 'Weekly':
                                var first = filter.from.getDate() - filter.from.getDay();
                                filter.from.setDate(first - 3);
                                $scope.updateFilter(filter);
                                break;
                            case 'Monthly':
                                filter.from.setMonth(filter.from.getMonth() - 1);
                                $scope.updateFilter(filter);
                                break;
                            case 'Yearly':
                                filter.from.setYear(filter.from.getYear() - 1 + 1900);
                                $scope.updateFilter(filter);
                                break;
                        }
                    }
                }

                $scope.dateNext = function (filter) {
                    if (['Daily', 'Weekly', 'Monthly', 'Yearly'].indexOf(filter.operator) >= 0) {
                        switch (filter.operator) {
                            case 'Daily':
                                filter.from.setDate(filter.from.getDate() + 1);
                                $scope.updateFilter(filter);
                                break;
                            case 'Weekly':
                                var first = filter.from.getDate() - filter.from.getDay();
                                filter.from.setDate(first + 10);
                                $scope.updateFilter(filter);
                                break;
                            case 'Monthly':

                                if (filter.options && filter.options.monthSpan) {
                                    monthSpan = (filter.options.monthSpan || 1) * 1;
                                } else {
                                    monthSpan = 1;
                                }

                                filter.from.setMonth(filter.from.getMonth() + monthSpan);
                                $scope.updateFilter(filter);
                                break;
                            case 'Yearly':
                                filter.from.setYear(filter.from.getYear() + 1 + 1900);
                                $scope.updateFilter(filter);
                                break;
                        }
                    }
                }

                $scope.changeValueFromDate = function (filter, from) {
                    filter[from + "Open"] = false;
                    $timeout(function () {
                        if (filter.operator == 'Less Than') {
                            filter.value = {
                                from: null,
                                to: dateFilter(filter.to, 'yyyy-MM-dd HH:mm:00')
                            };
                        } else if (filter.operator == 'More Than') {
                            filter.value = {
                                from: dateFilter(filter.from, 'yyyy-MM-dd HH:mm:00'),
                                to: null
                            };
                        } else {
                            filter.value = {
                                from: dateFilter(filter.from, 'yyyy-MM-dd HH:mm:00'),
                                to: dateFilter(filter.to, 'yyyy-MM-dd HH:mm:00')
                            };
                        }
                    }, 0);
                }
                
                $scope.focusDatePicker = function (filter, from) {
                    filter[from + "Open"] = true;
                }
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.operators = JSON.parse($el.find("data[name=operators]").text());
                $scope.options = JSON.parse($el.find("data[name=options]").text());
                $scope.filters = $scope.initFilters(JSON.parse($el.find("data[name=filters]").text()));
                $scope.oldFilters = null;
                $scope.datasource = $el.find("data[name=datasource]").text();
                $scope.datasources = JSON.parse($el.find("data[name=datasources]").text());

                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.renderID = $el.find("data[name=render_id]").text();
                $scope.dateOptions = {
                    'show-weeks': false
                };
                $scope.filterTemplate = {
                    string: 'filter_text',
                    number: 'filter_text',
                    list: 'filter_dropdown',
                    check: 'filter_dropdown',
                    relation: 'filter_dropdown',
                    date: 'filter_date'
                }
                parent[$scope.name] = $scope;
                $scope.available = false;
                
                if (!!$scope.options.freeze) {
                    var $container = $el.parents('.container-full');
                    var paddingLeft = $el.offset().left;
                    var width = $container.width() ;
                    $scope.freeze = function() {
                        var pl  =(($el.offset().left  *-1) + paddingLeft);
                        $el.css({
                            paddingLeft: pl + 'px',
                            width: (pl + width) + 'px'
                        });
                    }
                    $(window).resize(function () {
                        $timeout(function () {
                            width = $container.width();
                            $scope.freeze();
                        }, 400);
                    });
                    $container.scroll(function () {
                        $scope.freeze();
                    });
                }
                
                $scope.reset = function () {
                    $scope.resetPageSetting();
                    location.reload();
                }

                $scope.evalValue = function (value) {
                    if (typeof value == "string" && value.substr(0, 3) == "js:") {
                        return $scope.$parent.$eval(value.trim().substr(3));
                    }
                    return value;
                }
                $scope.ngIf = function (filter) {
                    if (!!filter.options && !!filter.options['ng-if']) {
                        return $scope.$parent.$eval(filter.options['ng-if']);
                    }
                    return true;
                }

                // Set Default Filters Value
                $timeout(function () {
                    var showCount = 0;
                    var watchDefaultValue = [];
                    if ($scope.isCached()) {
                        $scope.loadPageSetting();

                        for (i in $scope.filters) {
                            var f = $scope.filters[i];
                            if (!!$scope.oldFilters && !!$scope.oldFilters[i] &&
                                JSON.stringify(f[i]) != JSON.stringify($scope.oldFilters[i])) {

                                $scope.updateFilter(f, null, false);
                            }
                        }

                        $scope.datasources.map(function (dataSourceName) {
                            var ds = parent[dataSourceName];
                            if (ds) {
                                ds.lastQueryFrom = "DataFilter";
                                ds.disableTrackChanges('DataFilter:initDefaultValueCached');
                                ds.lastQueryFrom = "DataFilter";
                                ds.query();
                            }
                        });

                    } else {
                        for (i in $scope.filters) {
                            var f = $scope.filters[i];
                            var dateCondition = (f.filterType == 'date'
                            && ['Daily', 'Weekly', 'Monthly', 'Yearly']
                                .indexOf(f.defaultOperator) >= 0);

                            f.show = (typeof f.show == "boolean" ? f.show : (showCount > 5 ? false : true));
                            if ($scope.ngIf(f)) {
                                showCount++;
                            }

                            if (f.defaultValue && f.defaultValue != "" || dateCondition) {
                                if ($scope.operators[f.filterType]) {
                                    if (typeof f.defaultOperator != "undefined" && f.defaultOperator != "") {
                                        f.operator = f.defaultOperator;
                                        if (f.filterType == 'date') {
                                            if (f.defaultOperator == 'Between'
                                                || f.defaultOperator == 'Not Between') {
                                                f.from = $scope.evalValue(f.defaultValueFrom);
                                                f.to = $scope.evalValue(f.defaultValueTo);
                                            } else if (f.defaultOperator == 'Less Than') {
                                                f.to = $scope.evalValue(f.defaultValueTo);
                                                f.from = null;
                                            } else {
                                                f.from = $scope.evalValue(f.defaultValue);
                                                f.to = null;
                                            }
                                            
                                            f.value = {};
                                            if (typeof f.from == "string") {
                                                f.from = new Date(strtotime(f.from)  * 1000);
                                                f.value.from = f.from;
                                            }
                                            
                                            if (typeof f.to == "string") {
                                                f.to = new Date(strtotime(f.to) * 1000);
                                                f.value.to = f.to;
                                            }
                                            
                                        } else {
                                            f.value = $scope.evalValue(f.defaultValue);
                                        }
                                    }
                                    
                                    $scope.updateFilter(f, null, false);
                                }
                                else {
                                    f.value = $scope.evalValue(f.defaultValue);
                                    
                                    if (f.filterType === 'check') {
                                        if (typeof f.value === 'string') {
                                            f.valueText = f.value;
                                            f.value = f.value.trim().split(",");
                                            for (var fv in f.value) {
                                                f.value[fv] = f.value[fv].trim();
                                            }
                                            f.checked = f.value;
                                        }
                                        $scope.updateFilter(f, null, false);
                                    } else {
                                        if (!f.value) {
                                            var fclone = $.extend({}, f, true);
                                            watchDefaultValue.push({
                                                name: f.name,
                                                watch: fclone.defaultValue.substr(3)
                                            });
    
                                        } else {
                                            $scope.updateFilter(f, null, false);
                                        }
                                    }
                                }
                            }
                        }

                        watchDefaultValue.map(function (item, k) {
                            var unwatch = $scope.$parent.$watch(item.watch, function (n) {
                                if (!n)
                                    return;

                                for (i in $scope.filters) {
                                    if ($scope.filters[i].name == item.name) {
                                        $scope.filters[i].value = n;
                                        $scope.updateFilter($scope.filters[i], null);
                                        unwatch();
                                    }
                                }
                            });
                        });

                        $scope.datasources.map(function (dataSourceName) {
                            var ds = parent[dataSourceName];
                            if (ds) {
                                ds.lastQueryFrom = "DataFilter";
                                ds.disableTrackChanges('DataFilter:initDefaultValue');
                                ds.lastQueryFrom = "DataFilter";
                                ds.query();
                            }
                        });
                    }
                });
            }
        }
    };
});
