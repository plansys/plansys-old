app.directive('psActionBar', function ($timeout, $localStorage) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {
            $el.hide();
            $scope.init = false;

            if ($localStorage.portlet == null) {
                $localStorage.portlet = {};
            }
            if ($localStorage.portlet[$scope.pageUrl] == null) {
                $localStorage.portlet[$scope.pageUrl] = {};
            }
            var $portletLocal = $localStorage.portlet[$scope.pageUrl];

            if ($el.find('.data[name=portlets]').length > 0) {
                $scope.portlets = JSON.parse($el.find('.data[name=portlets]').text());
                $scope.portlets.forEach(function (item) {
                    if ($portletLocal[item.name] && !!$portletLocal[item.name].hide) {
                        item.hide = $portletLocal[item.name].hide;
                    }
                    if (item.hide) {
                        $timeout(function () {
                            $(".portlet-container[name=" + item.name + "]").hide();
                        })
                    }
                });
            }

            $scope.resetPage = function () {
                $scope.resetPageSetting();
                location.reload();
            }

            $scope.resetDashboard = function () {
                if (confirm("Your dashboard will now reset, are you sure?")) {
                    delete $localStorage.portlet[$scope.pageUrl];
                    $scope.resetPageSetting();
                    location.reload();
                }
            }

            $scope.togglePortlet = function (portlet, e) {

                e.preventDefault();
                e.stopPropagation();
                if ($portletLocal[portlet.name] == null) {
                    $portletLocal[portlet.name] = {};
                }
                var $portlet = $portletLocal[portlet.name];
                portlet.hide = !portlet.hide;
                $portlet.hide = portlet.hide;

                if (portlet.hide) {
                    $(".portlet-container[name=" + portlet.name + "]").hide();
                } else {
                    $(".portlet-container[name=" + portlet.name + "]").show();
                }
            }

            $(".ac-print").click(function () {
                $("#print-css").attr('href', $("#print-css").attr('u'));
                html2canvas(document.body, {
                    onrendered: function (canvas) {
                        $("#content").hide();
                        $("body").prepend(canvas);
                        $("body > canvas").click(function () {
                            location.reload();
                        });
                        window.print();
                    }
                });
            });

            $scope.originalHeight = $el.height();
            $(".ac-portlet-button").click(function () {
                var dd = $(this).parent().find('.ac-portlet-menu');
                $el.height('500');
                if (dd.css('position') != 'fixed') {
                    var pos = dd.offset();
                    var w = dd.width();
                    var h = dd.height() + 10;
                    dd.css({
                        top: 65,
                        left: pos.left + 3,
                        minWidth: w + 'px',
                        width: w + 'px',
                        height: h + 'px',
                        position: 'fixed',
                        zIndex: 999
                    });
                }
            });

            $(".ac-portlet-menu, .ac-portlet-button").hover(function () {
                $el.height(500);
            }, function () {
                $el.height($scope.originalHeight);
            });

            $scope.resizeTimeout = null;
            $scope.resize = function (st) {
                $timeout(function () {
                    var height = $scope.originalHeight;
                    var $container = $el.parents('.container-fluid').parent();
                    var woffset = $container.hasClass('container-full') ? 0 : 1;
                    
                    $el.css({
                        top: $container.offset().top - $container.css('marginTop').replace('px', '') * 1,
                        left: $container.offset().left + woffset,
                        width: $container.width() - woffset
                    });

                    if ($scope.form.layout.name == 'dashboard' && $el.parent().is('form')) {
                        var dashFilter = $el.parent().find('> [ps-data-filter]:eq(0)');

                        if (dashFilter.length > 0) {
                            dashFilter.css({
                                top: $("#content").offset().top + height,
                                width: $('#content').width(),
                                position: 'fixed',
                                opacity: .999
                            });
                            dashFilter.addClass('dash-filter');
                            height += dashFilter.height();
                            $el.addClass('filtered');
                        }
                    }

                    $container.css({
                        'margin-top': height + 'px',
                        'border-top': '0px'
                    });

                    $(".ac-portlet-btngroup.open").click();
                    $(".ac-portlet-menu").removeAttr('style').css('position', 'absolute');

                    if (!$scope.init) {
                        $scope.init = true;
                        $el.show();
                    }
                }, 100);
            };


            $(window).resize(function () {
                $scope.resize();
            });

            // add action tab link
            $(".section-header").each(function () {
                $('<a href="#' + $(this).attr('scrollTo') + '">' + $(this).text() + '</a>')
                        .insertBefore(".action-bar:eq(0) .action-tab .clearfix");
            })

            // on action tab click
            var container = $(".action-bar:eq(0)").parents(".container-full");
            $(".action-bar:eq(0) .action-tab a").click(function (e) {
                var top = container.scrollTop() + $($(this).attr('href')).position().top;
                container.scrollTop(top);
                e.preventDefault();
                return false;
            });

            function changeActive() {
                var active = null;
                if (container.scrollTop() == 0) {
                    active = $(".action-bar:eq(0) .action-tab a:first-child");
                } else if (container.scrollTop() == container[0].scrollHeight - container.height()) {
                    active = $(".action-bar:eq(0) .action-tab a").last();
                } else {
                    $(".action-bar:eq(0) .action-tab a").each(function () {
                        if ($($(this).attr('href')).length == 0) {
                            return;
                        }
                        var top = container.scrollTop() + $($(this).attr('href')).position().top;
                        if (container.scrollTop() >= top - 1) {
                            active = $(this);
                        }
                    });
                }

                if (active != null) {
                    $(".action-bar:eq(0) .action-tab a").removeClass("active");
                    active.addClass("active");
                }
            }

            // if there is #hash link, then go to hash sroll
            if (container.scrollTop() > 0) {
                setTimeout(function () {
                    changeActive();
                }, 100);
            }

            // on scroll
            angular.element(container).bind("scroll", function () {
                changeActive();
            });

        }
    }
});