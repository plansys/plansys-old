app.directive('dgRelation', function ($timeout, $compile, $http, $compile) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            return function ($scope, $el, attrs, ctrl) {
                $scope.list = [];
                $scope.loading = false;
                $scope.idx = 0;

                $scope.select = function (val, text) {
                    eval('$scope.$parent.' + attrs.ngModel.replace("_label", '') + ' = val');
                    eval('$scope.$parent.' + attrs.ngModel + ' = text');
                }
                $scope.match = [];

                $scope.doSearch = function () {
                    var search = Object.getProperty($scope.$parent, attrs.ngModel);
                    search = search || "";
                    var parentScope = angular.element($("#" + $scope.name)[0]).scope().$parent;
                    if (!$scope.loading) {
                        $scope.loading = true;
                        $http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                            's': search,
                            'm': $scope.modelClass,
                            'c': $scope.col.field,
                            'f': $scope.name,
                            'mf': parentScope.model,
                            'rf': $scope.row.entity
                        }).success(function (data) {
                            $timeout(function () {
                                var list = [];
                                for (i in data) {
                                    var item = {
                                        val: data[i].value,
                                        text: data[i].label
                                    };
                                    if (search.trim() == item.text.trim()) {
                                        $scope.idx = i;
                                    }
                                    list.push(item);
                                }

                                $scope.match = list;

                                $scope.loading = false;
                            }, 0);
                        }).error(function () {
                            $timeout(function () {
                                $scope.loading = false;
                            }, 0);
                        });
                    }
                }

                $scope.refocus = function () {
                    $timeout(function () {
                        var parentScope = angular.element($("#" + $scope.name)[0]).scope()
                        parentScope.lastFocus.focus();
                    }, 50);
                }
                $scope.close = function () {
                    if ($('.data-grid-dropdown li.hover').length == 0) {
                        $(".data-grid-dropdown").remove();
                        $(document).off(".dataGridAutocomplete");
                        $scope.select('', '');
                        $scope.refocus();
                    } else {
                        $timeout(function () {
                            $('.data-grid-dropdown li.hover').click();
                            $scope.refocus();
                        }, 0);
                    }
                }
                $el.keydown(function (e) {
                    $scope.$apply(function () {
                        switch (e.which) {
                            case 40:
                                $scope.idx++;
                                e.preventDefault();
                                return false;

                                break;
                            case 38:
                                $scope.idx--;

                                e.preventDefault();
                                return false;

                                break;
                            default:
                                break;
                        }
                    });
                });

                $el.keyup(function (e) {
                    if ([38, 40].indexOf(e.which) < 0) {
                        $scope.$apply(function () {
                            $scope.idx = 0;
                            $scope.doSearch();
                        });
                    }
                });

                $el.focus(function (e) {
                    var offset = $(e.target).offset();
                    var width = $(e.target).width() + 2;
                    offset.top += $(e.target).height() + 9;
                    var dd = '<ul ng-model="search" class="data-grid-dropdown dropdown-menu" style="width:' + width + 'px;top:' + offset.top + 'px;left:' + offset.left + 'px">';
                    dd += '<li ng-if="match.length > 0 " ng-repeat="item in match" class="dropdown-item" text="{{item.text}}" val="{{item.val}}" ng-class="{hover:$index==idx}">';
                    dd += '<a href="#">{{item.text}}</a></li>';
                    dd += '<div ng-if="match.length == 0"><center style="color:#999;font-size:12px;">&mdash; {{ loading ? "Loading" : "Not Found" }} &mdash;</center></div>';
                    dd += '</ul>';

                    $(dd).appendTo('body');

                    $timeout(function () {
                        $scope.doSearch();
                        $compile($(".data-grid-dropdown"))($scope);

                        $(document).on("click.dataGridAutocomplete", function () {
                            $scope.close();
                        });
                        $('.data-grid-dropdown').on('mouseover', 'li', function () {
                            $('.data-grid-dropdown li.hover').removeClass('hover');
                            $(this).addClass('hover');
                        });
                        $('.data-grid-dropdown').on('click', 'li', function () {
                            $scope.select($(this).attr('val'),$(this).attr('text'));
                            $(".data-grid-dropdown").remove();
                            $(document).off(".dataGridAutocomplete");
                        });
                    }, 0);
                });

                $el.blur(function () {
                    $scope.close();
                });

            }
        }
    }
});