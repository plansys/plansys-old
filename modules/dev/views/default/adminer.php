<iframe src="plansys/adminer.php?<?= $params ?>" frameborder="0" 
        style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;width:100%;height:100%;"></iframe>
<script type="text/javascript">
    $(function () {
        var def = window.location.href;
        function replaceQueryParam(param, newval, search) {
            var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?")
            var query = search.replace(regex, "$1").replace(/&$/, '')
            return (query.length > 2 ? query + "&" : "?") + param + "=" + newval
        }
        var def = window.location.href.split("dev/default/adminer")[0] + "dev/default/adminer";
        var init = false;

        $("iframe").load(function () {
            if (init) {
                var url = $("iframe")[0].contentWindow.location.href.split('?').pop();
                history.pushState(null, "Plansys Adminer", def + "&" + url);
            }
            init = true;
        });
    });
</script>