app.directive('portlet', function ($timeout, $compile, $http, $localStorage) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            $("#col1").css("padding-right", "17px");
            return function ($scope, $el, attrs, ctrl) {

                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.top = $el.find("data[name=top]:eq(0)").text();
                $scope.left = $el.find("data[name=left]:eq(0)").text();
                $scope.width = $el.find("data[name=width]:eq(0)").text();
                $scope.height = $el.find("data[name=height]:eq(0)").text();
                $scope.zoomable = $el.find("data[name=zoomable]:eq(0)").text().trim() == 'Yes';
                $scope.showBorder = $el.find("data[name=showBorder]:eq(0)").text().trim() == 'Yes';

                /************* Prepare LocalStorage *****************/
                if ($localStorage.portlet == null) {
                    $localStorage.portlet = {};
                }
                var $portlet = $localStorage.portlet;

                /** get url **/
                if ($portlet[$scope.pageUrl] == null) {
                    $portlet[$scope.pageUrl] = {};
                }
                $portlet = $portlet[$scope.pageUrl];

                /** get portlet name **/
                if ($portlet[$scope.name] == null) {
                    $portlet[$scope.name] = {};
                }
                $portlet = $portlet[$scope.name];
                $scope.portlet = $portlet;

                $scope.reset = function () {
                    if (!$scope.maximized) {
                        $timeout(function () {
                            $portlet.width = $portlet.original.width;
                            $portlet.height = $portlet.original.height;
                            $portlet.top = $portlet.original.top;
                            $portlet.left = $portlet.original.left;
                            $portlet.changed = false;
                            $el.css($portlet);
                            $scope.doneEdit();
                        });
                    }
                }

                $scope.doneEdit = function () {
                    $timeout(function () {
                        interact($el[0])
                                .draggable({enabled: false})
                                .resizable(false);

                        $scope.editing = false;
                    });
                }
                $scope.edit = function () {
                    $scope.editing = true;
                    interact($el[0])
                            .draggable({enabled: true})
                            .resizable(true);
                }

                $scope.maximize = function () {
                    $el.css($portlet);
                    $scope.maximized = true;
                }

                $scope.minimize = function () {
                    $scope.maximized = false;
                    $el.css($portlet);
                }

                $timeout(function () {
                    if (!isNaN($portlet.width) &&
                            !isNaN($portlet.height) &&
                            !isNaN($portlet.top) &&
                            !isNaN($portlet.left)) {
                        $el.css($portlet);
                    }

                    if (!$portlet.original)
                        $portlet.original = {};

                    $portlet.original.width = $scope.width * 1;
                    $portlet.original.height = $scope.height * 1;
                    $portlet.original.top = $scope.top == '' ? $el.position().top : $scope.top * 1;
                    $portlet.original.left = $scope.left == '' ? $el.position().left : $scope.left * 1;

                    if (!$portlet.changed) {
                        $portlet.left = $portlet.original.left;
                        $portlet.top = $portlet.original.top;
                        $portlet.width = $portlet.original.width;
                        $portlet.height = $portlet.original.height;
                    }
                    $el.css($portlet);

                    $timeout(function () {
                        $el.css('position', 'absolute');
                        interact($el[0]).draggable({
                            enabled: false,
                            inertia: true,
                            restrict: {
                                restriction: $("#col1 > .container-fluid")[0],
                                endOnly: true,
                                elementRect: {top: 0, left: 0, bottom: 1, right: 1}
                            }, onmove: function (event) {
                                if (!$scope.maximized) {
                                    $scope.vtop = $scope.vtop || $portlet.top * 1;
                                    $scope.vleft = $scope.vleft || $portlet.left * 1;
                                    $scope.vleft = $scope.vleft + event.dx;
                                    $scope.vtop = $scope.vtop + event.dy;

                                    $el.css('left', $scope.vleft);
                                    $el.css('top', $scope.vtop);
                                    $el.addClass('hover');

                                } else {
                                    $el.removeClass('hover');
                                }
                            }, onend: function (event) {
                                $portlet.changed = true;
                                $portlet.left = $scope.vleft;
                                $portlet.top = $scope.vtop;
                            }
                        }).resizable(false).on('resizemove', function (event) {
                            if (!$scope.maximized) {
                                $portlet.changed = true;
                                var target = event.target;
                                var newWidth = parseFloat(target.style.width) + event.dx;
                                var newHeight = parseFloat(target.style.height) + event.dy;
                                var container = $("#col1 > .container-fluid");
                                var maxW = container.width();

                                if (newWidth <= 150 ||
                                        newHeight <= 100 ||
                                        newWidth + target.style.left.replace('px', '') * 1 >= maxW) {
                                    return false;
                                }

                                target.style.width = newWidth + 'px';
                                target.style.height = newHeight + 'px';

                                $el.addClass('hover');
                            } else {
                                $el.removeClass('hover');
                            }

                            clearTimeout($scope.resizeTimeout);
                            $scope.resizeTimeout = setTimeout(function () {
                                $timeout(function () {
                                    $portlet.width = target.style.width.replace('px', '');
                                    $portlet.height = target.style.height.replace('px', '');
                                });
                            }, 100);
                        });

                        $el.css('opacity', 1);

                    });

                });

            }
        }
    }
});