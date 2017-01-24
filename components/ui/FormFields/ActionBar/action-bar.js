app.directive('psActionBar', function ($timeout, $localStorage) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {
            $el.hide();
            $scope.init = false;

            $scope.resetPage = function () {
                $scope.resetPageSetting();
                location.reload();
            }

            $scope.originalHeight = $el.height();

            $scope.resizeTimeout = null;
            $scope.resize = function (st) {
                var height = Math.min($scope.originalHeight, $el.height());
                var $container = $el.parents('.container-fluid').parent();
                
                if (!$container || !$container.offset()) {
                    return;
                }

                $el.css({
                    top: $container.offset().top - $container.css('marginTop').replace('px', '') * 1,
                    left: $container.offset().left,
                    width: $container.width()
                });

                $timeout(function () {
                    var woffset = $container.hasClass('container-full') ? 0 : 1;

                    $el.css({
                        top: $container.offset().top - $container.css('marginTop').replace('px', '') * 1,
                        left: $container.offset().left + woffset,
                        width: $container.width() - woffset
                    });
                }, 150);

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

                $(".action-bar-spacer").css({
                    'margin-top': height + 'px'
                });

                $(".ac-portlet-btngroup.open").click();
                $(".ac-portlet-menu").removeAttr('style').css('position', 'absolute');

                if (!$scope.init) {
                    $scope.init = true;
                    $el.show();
                }
            };
            

            $(window).resize(function () {
                $scope.resize();
            });

            // add action tab link
            $timeout(function() {
                $(".section-header").each(function () {
                    $('<a href="#' + $(this).attr('scrollTo') + '">' + $(this).text() + '</a>')
                        .insertBefore(".action-bar:eq(0) .action-tab .clearfix");
                })
            },250);

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