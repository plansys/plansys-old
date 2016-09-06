<!-- PLANSYS DB SUMMARY -->
<script type="text/javascript">

<?php
$items = [];
$total = 0;
foreach ($data as $index => $entry) {
    $query = explode("<|#-SEPARATOR-#|>", $entry[0]);
    $trace = json_decode(trim(@$query[0]), true);
    $query = trim(@$query[1]);

    $tracetext = [];
    if (is_array($trace)) {
        $tracetext[] = "---  " . " " . str_pad("File", 110) . " " . str_pad('Line', 6) . " " . str_pad('Called Class', 30) . " " . str_pad('Called Function', 25);

        foreach ($trace as $k => $t) {
            $tracetext[] = str_pad('#' . (count($trace) - $k), 5) . " " . str_pad($t['file'], 110) . " " . str_pad($t['line'], 6) . " " . str_pad($t['class'], 30) . " " . str_pad($t['function'], 25);
        }
    }

    $items[] = [
        'trace' => implode("\n", $tracetext),
        'query' => $query,
        'count' => sprintf('%5d', $entry[1]),
        'min'   => sprintf('%0.5f', $entry[2]),
        'max'   => sprintf('%0.5f', $entry[3]),
        'total' => sprintf('%0.5f', $entry[4]),
        'avg'   => sprintf('%0.5f', $entry[4] / $entry[1]),
        'data'  => $entry
    ];
}
array_shift($items);
?>
    (function () {
        if (typeof (console) == 'object') {
            var url = "";
            if (window.response) {
                url = " [" + window.response.config.url + "]";
                window.response = undefined;
            }

            var data = <?php echo json_encode($items); ?>;
            var time = 0;
            data.map(function (item) {
                time += item.total * 1;
            });

            function isframe() {
                try {
                    return window.self !== window.top;
                } catch (e) {
                    return true;
                }
            }
            
            if (isframe()) {
                url += "-iframe-";
            }
            
            if (data.length > 0) {
                console.groupCollapsed("DB Query Report [" + Math.round(time * 1000) + "ms] [" + data.length + " Query]" + url + ":");
                data.forEach(function (item, i) {
                    console.groupCollapsed("#" + (i + 1) + " [" + Math.round(item.total * 1000) + "ms] ");
                    console.info('%c' + item.query, 'font-size:10px;');
                    console.info('%c' + item.trace, 'font-size:10px;color:blue');
                    console.groupEnd();
                });
                console.groupEnd();
            }
        }
    })();
</script>