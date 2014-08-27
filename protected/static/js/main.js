$(function() {
    $("#mainmenu ul ul li.active").parent().parent().addClass("active");
    $("#mainmenu ul ul").hover(function() {
        $(this).parent().addClass("active");
    }, function() {
        $(this).parent().removeClass("active");
        $("#mainmenu ul ul li.active").parent().parent().addClass("active");
    });
    
});