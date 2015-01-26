app.directive('psActionBar', function ($timeout, $localStorage) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {

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

            $(".ac-portlet-button").click(function () {
                var dd = $(this).parent().find('.ac-portlet-menu');

                var height = $el.height();
                $el.height('500');
                if (dd.css('position') != 'fixed') {
                    var pos = dd.offset();
                    var w = dd.width();
                    var h = dd.height() + 10;
                    dd.css({
                        top: pos.top,
                        left: pos.left + 3,
                        minWidth: w + 'px',
                        width: w + 'px',
                        height: h + 'px',
                        position: 'fixed',
                        zIndex: 110
                    });
                }
                $timeout(function () {
                    $el.height(height);
                });
            });

            $scope.resize = function (st) {
                var height = $el.height();
                $el.css({
                    top: $("#content").offset().top,
                    left: $el.parents('.container-full').offset().left,
                    width: $('#content').width(),
                    opacity: .999
                });

                $timeout(function () {
                    $el.css({opacity: 1});
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

                $el.parents('.container-full').css({
                    'margin-top': height + 'px',
                    'border-top': '0px'
                });

                $(".ac-portlet-btngroup.open").click();
                $(".ac-portlet-menu").removeAttr('style').css('position', 'absolute');
            }

            $scope.resize('init');

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