app.directive('dgAutocomplete', function ($timeout, $compile, $http, $compile) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            var fuzzyMatch = {
                pattern: '',
                prep: function (s) {
                    this.pattern = new RegExp('(' + s.split('').join(').*?(') + ').*?', 'i');
                    return this;
                },
                match: function (s) {
                    return s.match(this.pattern) != null;
                },
                hi: function (s) {
                    return s.replace(
                            this.pattern,
                            this._hi
                            );
                },
                _hi: function () {
                    var match = arguments[0];
                    var max = arguments.length - 2;
                    for (; max > 0; max--) {
                        var r = new RegExp('(^|[^÷])(' + arguments[max] + ')');
                        match = match.replace(r, '$1÷$2þ');
                    }
                    return match.replace(/þ/g, '</u>').replace(/÷/g, '<u>');
                }
            };

            return function ($scope, $el, attrs, ctrl) {

                $scope.list = [];
                try {
                    $scope.list = JSON.parse($('#' + attrs.dgaId).text());
                } catch (e) {
                    $scope.list = [];
                }

                if (typeof $scope.list == "object") {
                    $scope.list = $.map($scope.list, function (value, index) {
                        return [value];
                    });
                }


                $scope.idx = 0;

                $scope.select = function (val) {
                    eval('$scope.$parent.' + attrs.ngModel + ' = val');
                }
                $scope.match = [];

                $scope.doSearch = function (search) {
                    if (typeof search == "undefined") {
                        search = Object.getProperty($scope.$parent, attrs.ngModel);
                    }
                    fuzzyMatch.prep(search);
                    $scope.match = [];

                    $scope.list.filter(function (item) {
                        if (fuzzyMatch.match(item)) {
                            if (typeof item == "string") {
                                $scope.match.push({
                                    val: item,
                                    text: item,
                                    html: fuzzyMatch.hi(item)
                                });
                            }
                        }
                    });
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

                        if (attrs.dgaMustChoose === "Yes") {
                            $scope.select('');
                        }
                        $scope.refocus();
                    } else {
                        $timeout(function () {
                            $('.data-grid-dropdown li.hover').click();
                            $scope.refocus();
                        }, 0);
                    }
                }
                $el.keydown(function (e) {

                    function scroll() {
                        $timeout(function () {
                            var elHeight = $('.data-grid-dropdown li.hover').height();
                            $('.data-grid-dropdown').scrollTop($scope.idx * elHeight);
                        }, 0);
                    }

                    $scope.$apply(function () {
                        switch (e.which) {
                            case 40:
                                $scope.idx++;

                                scroll();
                                e.preventDefault();
                                return false;

                                break;
                            case 38:
                                $scope.idx--;

                                scroll();
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
                    dd += '<li ng-if="match.length > 0 " ng-repeat="item in match" class="dropdown-item" val="{{item.val}}" ng-class="{hover:$index==idx}">';
                    dd += '<a href="#" ng-bind-html="item.html"></a></li>';
                    dd += '<div ng-if="match.length == 0"><center style="color:#999;font-size:12px;">&mdash; Not Found &mdash;</center></div>';
                    dd += '</ul>';

                    $(dd).appendTo('body');

                    $timeout(function () {

                        $scope.doSearch("");
                        $compile($(".data-grid-dropdown"))($scope);

                        $(document).on("click.dataGridAutocomplete", function () {
                            $scope.close();
                        });
                        $('.data-grid-dropdown').on('mouseover', 'li', function () {
                            $('.data-grid-dropdown li.hover').removeClass('hover');
                            $(this).addClass('hover');
                        });
                        $('.data-grid-dropdown').on('click', 'li', function () {
                            $scope.select($(this).attr('val'));
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