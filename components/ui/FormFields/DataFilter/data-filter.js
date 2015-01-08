app.directive('psDataFilter', function ($timeout, dateFilter, $http) {
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
                var parent = $scope.$parent;

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

                $scope.resetFilter = function (filter) {
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
                            }

                            ds.query(function () {
                                delete ds.afterQueryInternal[$scope.renderID];
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
                            // todo: fix search focus
                        }
                    }
                    return filters;
                }

                $scope.prepareDSParams = function (filter) {
                    var prepared = angular.copy(filter);
                    prepared.name = filter.name;
                    prepared.value = filter.value;
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
                            if (filter.value != "") {
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
                                }

                                ds.query(function () {
                                    delete ds.afterQueryInternal[$scope.renderID];
                                });
                            }
                        }
                    });
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


                $scope.listSearch = function (e, filter) {
                    if (filter.filterType == "relation") {
                        $timeout(function () {
                            $scope.loading = true;
                            $http.post(Yii.app.createUrl('formfield/DataFilter.relnext'), {
                                's': filter.search,
                                'f': $scope.name,
                                'n': filter.name,
                                'm': $scope.modelClass,
                                'i': 0
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
                
                $scope.relationNext = function (e, filter) {
                    e.stopPropagation();
                    e.preventDefault();

                    $scope.loading = true;
                    $http.post(Yii.app.createUrl('formfield/DataFilter.relnext'), {
                        's': filter.search,
                        'f': $scope.name,
                        'n': filter.name,
                        'm': $scope.modelClass,
                        'i': filter.list.length
                    }).success(function (data) {
                        $scope.loading = false;
                        if (data.list && data.list.length && data.list.length > 0) {
                            data.list.forEach(function (item) {
                                filter.list.push(item);
                            });
                        }
                    });

                    return false;
                }


                $scope.toggleShowFilter = function (filter) {
                    filter.show = (filter.show ? false : true);
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

                    $scope.updateFilter(filter, e);
                }

                $scope.dropdownChecked = function (filter, item) {
                    if (filter.valueText == 'All') {
                        return false;
                    } else if (filter.checked.indexOf(item.key) >= 0) {
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
                            newList.push({key: key, value: list[key]});
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
                                if (typeof from == "undefined") {
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

                        if (typeof $scope.filters[idx].valueText == "string") {
                            ret = date("Y-m-d", strtotime($scope.filters[idx].valueText));
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
                        filter.value = {
                            from: dateFilter(filter.from, 'yyyy-MM-dd HH:mm:00'),
                            to: dateFilter(filter.to, 'yyyy-MM-dd HH:mm:00')
                        };
                    }, 0);
                }

                $scope.focusDatePicker = function (filter, from) {
                    filter[from + "Open"] = true;
                }
                $scope.modelClass = $el.find("data[name=model_class]").html();
                $scope.operators = JSON.parse($el.find("data[name=operators]").text());
                $scope.filters = $scope.initFilters(JSON.parse($el.find("data[name=filters]").text()));
                $scope.datasource = $el.find("data[name=datasource]").text();
                $scope.datasources = JSON.parse($el.find("data[name=datasources]").text());
                $scope.name = $el.find("data[name=name]").text();
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
                    var ds = parent[$scope.datasource];
                    var dataAvailable = ds && ds.data != null && ds.data.length > 0;
                    var watchDefaultValue = [];
                    var defaultValueAvailable = false;
                    for (i in $scope.filters) {
                        var f = $scope.filters[i];
                        var dateCondition = (f.filterType == 'date'
                                && ['Daily', 'Weekly', 'Monthly', 'Yearly']
                                .indexOf(f.defaultOperator) >= 0);

                        f.show = (showCount > 5 ? false : true);
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
                                        } else {
                                            f.from = $scope.evalValue(f.defaultValue);
                                        }
                                    } else {
                                        f.value = $scope.evalValue(f.defaultValue);
                                    }
                                }

                                $scope.updateFilter(f, null, false);
                                defaultValueAvailable = true;
                            }
                            else {
                                f.value = $scope.evalValue(f.defaultValue);
                                if (!f.value) {
                                    var fclone = $.extend({}, f, true);
                                    watchDefaultValue.push({
                                        name: f.name,
                                        watch: fclone.defaultValue.substr(3)
                                    });
                                } else {
                                    $scope.updateFilter(f, null, false);
                                    defaultValueAvailable = true;
                                }
                            }
                        }
                    }

                    watchDefaultValue.map(function (item, k) {
                        var a = $scope.$parent.$watch(item.watch, function (n) {
                            if (!n)
                                return;

                            for (i in $scope.filters) {
                                if ($scope.filters[i].name == item.name) {
                                    $scope.filters[i].value = n;
                                    $scope.updateFilter($scope.filters[i], null);
                                    a();
                                }
                            }
                        });
                    });

                    if (defaultValueAvailable) {
                        $scope.datasources.map(function (dataSourceName) {
                            var ds = parent[dataSourceName];
                            if (ds) {
                                ds.query();
                            }
                        });
                    }
                });

            }
        }
    };
});