
<script>
    app.controller("<?= $class ?>MenuTree", ["$scope", "$http", "$timeout", "$templateCache", function ($scope, $http, $timeout, $templateCache) {
            $scope.list = <?= json_encode($list); ?>;
            $scope.active = null;
            $scope.sections = <?= json_encode($sections); ?>;
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

            $scope.select = function (item) {
                this.toggle();
                item.state = '';
                $scope.selecting = true;
                $scope.active = item;
            };
            $scope.openContextMenu = function (item, e, itemTree) {
                if ($scope.originalContextMenu == null) {
                    $scope.originalContextMenu = angular.copy($scope.contextMenu);
                }

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
            }


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
<?= $inlineJS ?>


        }
    ]);
</script>