app.directive('portlet', function ($timeout, $compile, $http, $localStorage) {
    return {
        scope: true,
        compile: function (element, attrs, transclude) {
            $("#col1").css("padding-right", "17px");
            return function ($scope, $el, attrs, ctrl) {
                $(".dashboard-mode").show();
                $scope.name = $el.find("data[name=name]:eq(0)").text();
                $scope.top = $el.find("data[name=top]:eq(0)").text();
                $scope.left = $el.find("data[name=left]:eq(0)").text();
                $scope.width = $el.find("data[name=width]:eq(0)").text();
                $scope.height = $el.find("data[name=height]:eq(0)").text();
                $scope.zoomable = $el.find("data[name=zoomable]:eq(0)").text().trim() == 'Yes';
                $scope.showBorder = $el.find("data[name=showBorder]:eq(0)").text().trim() == 'Yes';

                $scope.$storage = $localStorage;
                if (!$scope.$storage.plansysFormBuilder || !$scope.$storage.plansysFormBuilder[$scope.params.classPath]) {
                    $("#must-reload").show();
                    $("#must-reload h3").html('Warning<br/><small>Dashboard Mode can only be<br/> opened through FormBuilder</small>');
                    $("#must-reload .btn").hide();
                    return false;
                }

                if (!$scope.$parent.fields) {
                    // load fields from server fields (params.fields)
                    $scope.$parent.fields = $scope.$storage.plansysFormBuilder[$scope.params.classPath];
                    $scope.$parent.fields.length = 0;
                    $scope.params.fields.forEach(function (item) {
                        $scope.$parent.fields.push(item);
                    });
                }

                $scope.fields = $scope.$parent.fields;

                $scope.searchPortlet = function (search) {
                    if (typeof search == "undefined") {
                        search = $scope.$storage.plansysFormBuilder[$scope.params.classPath];
                    }

                    for (var i in search) {
                        var item = search[i];
                        if (typeof item != "object" || item == null)
                            continue;

                        if (item.name === $scope.name) {
                            return item;
                        }

                        for (var k in item) {
                            var subitem = item[k];
                            if (typeof subitem != "object" || !subitem || subitem == null)
                                continue;

                            var result = $scope.searchPortlet(subitem);
                            if (result)
                                return result;
                        }
                    }
                }
                $scope.localPortlet = $scope.searchPortlet();

                $scope.$watch('$storage', function () {
                    if ($scope.$storage.plansysFormBuilder[$scope.params.classPath] !== $scope.fields) {
                        location.reload();
                    }
                }, true);

                var $portlet = {
                    top: $scope.localPortlet.top,
                    left: $scope.localPortlet.left,
                    width: $scope.localPortlet.width,
                    height: $scope.localPortlet.height,
                };
                $scope.portlet = $portlet;

                $scope.reset = function () {
                    if (!$scope.maximized) {
                        $timeout(function () {
                            $portlet.width = $portlet.original.width;
                            $portlet.height = $portlet.original.height;
                            $portlet.top = $portlet.original.top;
                            $portlet.left = $portlet.original.left;

                            $el.css($portlet);
                        });
                    }
                }

                $scope.save = function () {
                    $(".dashboard-saving").show();
                    var url = Yii.app.createUrl('/dev/forms/save', {class: $scope.params.classPath});
                    $scope.localPortlet = $scope.searchPortlet();

                    $scope.localPortlet.top = Math.round($portlet.top);
                    $scope.localPortlet.left = Math.round($portlet.left);
                    $scope.localPortlet.width = Math.round($portlet.width);
                    $scope.localPortlet.height = Math.round($portlet.height);

                    $http.post(url, {fields: $scope.fields})
                            .success(function (data, status) {
                                if (data == "FAILED") {
                                    $("#must-reload h3").html('Warning<br/><small>Dashboard Data is inconsistent<br/> Please open this page through FormBuilder</small>');
                                    $("#must-reload .btn").hide();
                                }
                                $(".dashboard-saving").hide();
                            })
                            .error(function (data, status) {
                                $(".dashboard-saving").hide();
                            });
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
                    } else {
                        $portlet.width = $scope.width * 1;
                        $portlet.height = $scope.height * 1;
                        $portlet.top = $scope.top == '' ? $el.position().top : $scope.top * 1;
                        $portlet.left = $scope.left == '' ? $el.position().left : $scope.left * 1;
                        $portlet.original = angular.copy($portlet);

                        $el.css($portlet);
                    }

                    $timeout(function () {
                        $el.css('position', 'absolute');
                        interact($el[0]).draggable({
                            inertia: true,
                            onmove: function (event) {
                                if (!$scope.maximized) {
                                    $scope.vtop = $scope.vtop || $portlet.top * 1;
                                    $scope.vleft = $scope.vleft || $portlet.left * 1;
                                    $scope.vleft = $scope.vleft + event.dx;
                                    $scope.vtop = $scope.vtop + event.dy;

                                    $el.css('left', $scope.vleft);
                                    $el.css('top', $scope.vtop);
                                    $el.addClass('hover');

                                    $timeout(function () {
                                        $portlet.left = $scope.vleft;
                                        $portlet.top = $scope.vtop;
                                    });
                                } else {
                                    $el.removeClass('hover');
                                }
                                $scope.dragging = true;
                            }, onend: function (event) {
                                $scope.dragging = false;
                                $scope.save();
                            }
                        }).resizable(true).on('resizemove', function (event) {
                            if (!$scope.maximized) {
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

                                $timeout(function () {
                                    $portlet.width = target.style.width.replace('px', '');
                                    $portlet.height = target.style.height.replace('px', '');
                                });
                                $el.addClass('hover');
                            } else {
                                $el.removeClass('hover');
                            }

                            clearTimeout($scope.resizeTimeout);
                            $scope.resizeTimeout = setTimeout(function () {
                                $scope.save();
                            }, 100);
                        });
                        $el.css('opacity', 1);
                    });
                });

            }
        }
    }
});