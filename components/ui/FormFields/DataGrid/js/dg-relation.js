app.directive('dgRelation', function ($timeout, $compile, $http, $compile) {
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
                $scope.loading = false;
                $scope.idx = 0;

                $scope.original = {
                    label: Object.getProperty($scope.$parent, attrs.ngModel),
                    id: Object.getProperty($scope.$parent, attrs.ngModel.replace("_label", ''))
                };

                $scope.select = function (val, text) {
                    eval('$scope.$parent.' + attrs.ngModel.replace("_label", '') + ' = val');
                    eval('$scope.$parent.' + attrs.ngModel + ' = text');
                }
                $scope.match = [];
                $scope.paramValue = {};
                
                
                $scope.params = JSON.parse(attrs.params);
                for (i in $scope.params) {
                    var p = $scope.params[i];
                    if (p.indexOf('js:') === 0) {
                        var value = $scope.$parent.$eval(p.replace('js:', ''));
                        var key = i;
                        $scope.$parent.$watch(p.replace('js:', ''), function (newv, oldv) {
                            if (newv != oldv) {
                                $scope.paramValue[key] = newv;
                                $scope.doSearch();
                            }
                        }, true);
                        $scope.paramValue[key] = value;
                    }
                }
                
                $scope.doSearch = function () {
                    var search = Object.getProperty($scope.$parent, attrs.ngModel);
                    search = search || "";
                    var parentScope = angular.element($("#" + $scope.name)[0]).scope().$parent;
                    if (!$scope.loading) {
                        $scope.loading = true;
                        $http.post(Yii.app.createUrl('formfield/RelationField.dgrSearch'), {
                            's': search,
                            'm': $scope.modelClass,
                            'f': $scope.name,
                            'c': $scope.col.field,
                            'p': $scope.paramValue
                        }).success(function (data) {
                            $timeout(function () {
                                var list = [];
                                fuzzyMatch.prep(search);
                                for (i in data) {
                                    var item = {
                                        val: data[i].value,
                                        text: data[i].label,
                                        html: fuzzyMatch.hi(data[i].label)
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
                $scope.escaped = true;

                $scope.close = function () {
                    if ($('.data-grid-dropdown li.hover').length == 0 || $scope.escaped) {
                        $(".data-grid-dropdown").remove();
                        $(document).off(".dataGridAutocomplete");
                        
                        $timeout(function () {
                            $scope.select($scope.original.id, $scope.original.label);
                            $scope.refocus();
                        }, 0);
                    } else {
                        $timeout(function () {
                            $('.data-grid-dropdown li.hover').click();
                            $scope.refocus();
                        }, 0);
                    }
                    $scope.escaped = true;
                }
                
                $el.keydown(function (e) {
                    $scope.escaped = false;
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
                    $scope.escaped = false;
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
                    dd += '<a href="#" ng-bind-html="item.html"></a></li>';
                    dd += '<div ng-if="match.length == 0"><center style="color:#999;font-size:12px;" ng-cloak>&mdash; {{ loading ? "Loading" : "Not Found" }} &mdash;</center></div>';
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
                            $scope.escaped = false;
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