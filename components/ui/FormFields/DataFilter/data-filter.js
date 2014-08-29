app.directive('psDataFilter', function($timeout) {
    return {
        scope: true,
        compile: function(element, attrs, transclude) {
            //hide filter criteria when mouse is clicked outside
            $(document).mouseup(function(e) {
                var container = $(".filter-criteria");
                if (!container.is(e.target) && container.parent().has(e.target).length === 0) {
                    container.hide().removeClass('open');
                    $(".filter-criteria-button").removeClass('active');
                }
            });

            return function($scope, $el, attrs, ctrl) {
                $scope.focused = function(e) {
                    $(e.target).parents('.input-group').find('.focused').focus();
                }

                $scope.toggleFilterCriteria = function(e) {
                    var parent = $(e.target).parents('.btn-group');
                    var btn = parent.find('.filter-criteria-button');
                    if (parent.find("> .filter-criteria").hasClass('open')) {
                        parent.find("> .filter-criteria").removeClass('open');
                        parent.find("> .filter-criteria").hide();
                        btn.removeClass('active');
                    } else {
                        //hide another filter criteria
                        $(".filter-criteria").hide().removeClass('open');
                        $(".filter-criteria-button").removeClass('active');
                        btn.addClass('active');

                        //show current filter criteria
                        parent.find("> .filter-criteria").addClass('open');
                        parent.find("> .filter-criteria").show();
                        parent.find('.focused').focus();


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

                $scope.toggleShowFilter = function(filter) {
                    filter.show = (filter.show ? false : true);
                }

                $scope.initFilters = function(filters) {
                    for (i in filters) {
                        var f = filters[i];
                        f.value = '';
                        f.show = true;
                        f.valueText = 'All';
                        f.operatorDropdownOpen = false;
                        f.operator = $scope.operators[f.filterType][0];
                    }
                    return filters;
                }

                $scope.updateFilter = function(filter, e) {
                    $scope.changeValueText(filter)
                    $scope.toggleFilterCriteria(e);
                }

                $scope.filterValueKeydown = function(filter, e) {
                    switch (e.which) {
                        case 13:
                            e.preventDefault();
                            e.stopPropagation();
                            $scope.updateFilter(filter, e);
                            break;
                    }
                }

                $scope.changeValueText = function(filter) {
                    if (filter.value == '') {
                        filter.valueText = 'All';
                    } else {
                        filter.valueText = filter.operator + " [" + filter.value + "]";
                    }
                }

                $scope.changeOperator = function(filter, operator, e) {
                    filter.operator = operator;
                    filter.operatorDropdownOpen = false;
                    $scope.focused(e);

                    $scope.changeValueText(filter, filter.value);
                }

                $scope.operators = JSON.parse($el.find("data[name=operators]").text());
                $scope.filters = $scope.initFilters(JSON.parse($el.find("data[name=filters]").text()));
                $scope.filterTemplate = {
                    string: 'filter_common',
                    number: 'filter_common',
                    date: 'filter-date'
                }
            }
        }
    };
});