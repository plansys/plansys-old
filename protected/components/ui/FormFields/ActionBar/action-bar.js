$(function() {
    if ($(".action-bar").length > 0) {

        $(".action-bar").each(function() {
            var height = $(this).height();
            $(this).css('top', '32px');
            $(this).parent().parent().parent().css({
                'margin-top': height + 'px',
                'border-top': '0px'
            });
        });

        // add action tab link
        $(".section-header").each(function() {
            $('<a href="#' + $(this).attr('scrollTo') + '">' + $(this).text() + '</a>')
                    .insertBefore(".action-bar:eq(0) .action-tab .clearfix");
        })

        // on action tab click
        var container = $(".action-bar:eq(0)").parents(".container-full");
        $(".action-bar:eq(0) .action-tab a").click(function(e) {
            var top = container.scrollTop() + $($(this).attr('href')).position().top;
            container.scrollTop(top);

            e.preventDefault();
            return false;
        });

        function changeActive() {
            var active = null;

            if (container.scrollTop() == 0) {
                active = $(".action-bar:eq(0) .action-tab a:first-child");
                console.log(container.scrollTop());
            } else if (container.scrollTop() == container[0].scrollHeight - container.height()) {
                active = $(".action-bar:eq(0) .action-tab a").last();
            } else {
                $(".action-bar:eq(0) .action-tab a").each(function() {
                    var top = container.scrollTop() + $($(this).attr('href')).position().top;
                    console.log($(this).text(), container.scrollTop(), top);
                    if (container.scrollTop() >= top) {
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
            $timeout(function() {
                changeActive();
            }, 100);
        }

        // on scroll
        angular.element(container).bind("scroll", function() {
            changeActive();
        });

    }
});