app.directive('psActionBar', function ($timeout) {
    return {
        scope: true,
        link: function ($scope, $el, attrs) {

            if ($el.find('.data[name=portlets]').length > 0) {
                $scope.portlets = JSON.parse($el.find('.data[name=portlets]').text());
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
            });

            $(window).resize(function () {
                $(".action-bar-container").each(function () {
                    var height = $(this).height();
                    $(this).css({
                        top: $("#content").offset().top,
                        left: $(this).parents('.container-full').offset().left,
                        width: $(this).parents('[ui-layout]').width()
                    });
                    $(this).parents('.container-full').css({
                        'margin-top': height + 'px',
                        'border-top': '0px'
                    });
                });
                $(".ac-portlet-btngroup.open").click();
                $(".ac-portlet-menu").removeAttr('style').css('position', 'absolute');
            }).resize();

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