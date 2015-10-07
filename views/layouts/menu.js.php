
<script>
    app.controller("<?php echo $class ?>MenuTree", ["$scope", "$compile", "$http", "$location", "$timeout", "$templateCache", function ($scope, $compile, $http, $location, $timeout, $templateCache) {
            $scope.list = <?php echo json_encode($list); ?>;
            $scope.active = null;
            $scope.sections = <?php echo json_encode($sections); ?>;
            $scope.selecting = false;
            $scope.targetSection = null;
            $scope.targetHTML = '';

            /******************* CONTEXT MENU SECTION ********************/
            $scope.contextMenu = [];
            $scope.contextMenuActive = null;
            $scope.contextMenuDisabled = false;
            $scope.contextMenuVisibleCount = 0;
            $scope.originalContextMenu = null;

            $scope.executeMenu = function (func, item, e) {
                if (typeof func == "function") {
                    $timeout(function () {
                        func($scope.contextMenuActive, e);
                    });
                }
            }
            $scope.processContextMenu = function (item, menu, orig, menuParent) {
                if (typeof orig == "object") {
                    menu.$parent = parent;

                    switch (typeof orig.label) {
                        case "string":
                            menu.label = orig.label;
                            break;
                        case "function":
                            menu.label = orig.label(item, orig);
                            break;
                    }

                    switch (typeof orig.visible) {
                        case "boolean":
                            menu.visible = orig.visible;
                            break;
                        case "function":
                            menu.visible = orig.visible(item, orig);
                            break;
                        default:
                            menu.visible = true;
                            break;
                    }
                }
                return false;
            };

            $scope.recurseContextMenu = function (item, menus, orig) {
                for (i in menus) {
                    $scope.processContextMenu(item, menus[i], orig[i], menus);

                    if (menus[i].visible) {
                        $scope.contextMenuVisibleCount++;
                    }
                }
            };

            $scope.UpdateQueryString = function (key, value, url) {
                if (!url)
                    url = window.location.href;
                var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                        hash;

                if (re.test(url)) {
                    if (typeof value !== 'undefined' && value !== null)
                        return url.replace(re, '$1' + key + "=" + value + '$2$3');
                    else {
                        hash = url.split('#');
                        url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                        if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                            url += '#' + hash[1];
                        return url;
                    }
                }
                else {
                    if (typeof value !== 'undefined' && value !== null) {
                        var separator = url.indexOf('?') !== -1 ? '&' : '?';
                        hash = url.split('#');
                        url = hash[0] + separator + key + '=' + value;
                        if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                            url += '#' + hash[1];
                        return url;
                    }
                    else
                        return url;
                }
            };

            $scope.assignParentToItems = function(item, parent) {
                if (item.length > 0) {
                    item.forEach(function(children) {
                        $scope.assignParentToItems(children);
                    });
                } else {
                    if (!!item.items && item.items.length > 0) {
                        item.items.forEach(function (children) {
                            $scope.assignParentToItems(children, item);
                        });
                    }

                    if (!!parent) {
                        item.$parent = parent;
                    }
                }
            }
            $scope.assignParentToItems($scope.list);

            $scope.expandAllParents = function(item) {
                item.state = '';
                if (item.$parent) {
                    $scope.expandAllParents(item.$parent);
                }
            }

            var unwatchActive =  $scope.$watch('active', function(e) {
                if (!!e) {
                    $scope.expandAllParents(e);
                    unwatchActive();
                }
            });

            $scope.select = function (item, e) {
                $scope.closeContextMenu();

                this.toggle();
                item.state = '';
                $scope.selecting = true;
                $scope.active = item;

                if (!!$scope.sections[item.target] && !!e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!!item.url && !!item.target) {
                        var controller = angular.element("#" + item.target + ':eq(0) [ng-controller]:eq(0)');
                        var scope = controller.scope();
                        if (!!scope) {
                            // sementara di-redirect biasa dulu, ga usah pake ajax
                            location.href = item.url;

//                          TODO: find performance degradation bug...
//                          
//                            var url = $scope.UpdateQueryString('render_section', item.target, item.url);
//
//                            var loadingHtml = '<div class="loading"><span><b> ';
//                            loadingHtml += '<i class="fa fa-refresh fa-spin"></i>  Loading ';
//                            loadingHtml += item.label;
//                            loadingHtml += '... </b> </span> </div>';
//                            angular.element("#" + item.target + ':eq(0)').html(loadingHtml);
//                            $http.get(url).success(function (data) {
//                                var html = $(data).find('#' + item.target + ':eq(0)').html();
//                                angular.element("#" + item.target + ':eq(0)').html(html);
//                                $compile("#" + item.target + ':eq(0)  > div')(scope);
//                                history.pushState(null, '', item.url);
//                            });
                        }
                    }
                }
            };

            $scope.closeContextMenu = function () {
                $(".menu-sel").removeClass("active").removeClass(".menu-sel");
                $("#ContextMenu<?php echo $class ?>").removeClass('open');
                return false;
            }

            $scope.openContextMenu = function (item, e, itemTree) {
                if ($scope.originalContextMenu == null) {
                    $scope.originalContextMenu = angular.copy($scope.contextMenu);
                }

                item.$tree = itemTree;
                if (itemTree.$parentNodeScope != null) {
                    item.$parent = itemTree.$parentNodeScope.$modelValue;
                }

                // mark visible menu
                $scope.contextMenuVisibleCount = 0;
                $scope.recurseContextMenu(item, $scope.contextMenu, $scope.originalContextMenu);
                $scope.contextMenuDisabled = ($scope.contextMenuVisibleCount == 0);

                // reset item state, collapsed or expanded ('' means expanded)
                item.state = '';

                // set menu as active
                $(".menu-sel").removeClass("active").removeClass(".menu-sel");
                $(e.target).parent().addClass("menu-sel active");
                $scope.contextMenuActive = item;
            };

            /******************* MENU TREE SECTION ********************/
            $scope.getUrl = function (item) {
                return item.url || '#';
            };
            $scope.getTarget = function (item) {
                if (!!$scope.sections[item.target]) {
                    return '_self';
                }
                return item.target || '_self';
            };
            $scope.iconAvailable = function (item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            };
            $scope.isSelected = function (item) {
                return angular.equals(item, $scope.active) ? 'active' : '';
            };


            /******************* INLINEJS SECTION ********************/
<?php echo $inlineJS ?>


        }
    ]);
</script>