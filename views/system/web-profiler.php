<!-- PLANSYS DEBUG SUMMARY -->
<script type="text/javascript">
<?php
$items     = [];
$total     = 0;
$startTime = $data[0][3];
$lastTime  = $startTime;
$time      = $data[count($data) - 1][3] - $startTime;
foreach ($data as $index => $entry) {
    $item = [
        'trace'    => $entry[0],
        'level'    => $entry[1],
        'path'     => $entry[2],
        'lastTime' => $lastTime,
        'time'     => $entry[3] - $lastTime,
        'ellapsed' => $entry[3] - $startTime
    ];

    $firstline    = trim(explode("\n", substr($item['trace'], 0, 50))[0]);
    if (strpos($firstline, 'Querying SQL') === 0) {
        $firstline = 'Querying SQL';
    }
    $item['text'] = "#" . str_pad($index, 2) .
            " [" . str_pad($item['level'], 9, " ", STR_PAD_BOTH) . "]" .
            ($index > 0 ? " [+" . str_pad(round($item['time'] * 1000, 1) . "ms", 9, " ", STR_PAD_LEFT) . "]" : str_pad(" ", 13)) .
            " [" . str_pad(round($item['ellapsed'] * 1000, 1) . "ms", 9, " ", STR_PAD_LEFT) . "]" .
            " " . str_pad($firstline, 60) .
            " [" . $item['path'] . "]";
    $items[]      = $item;
    $lastTime     = $entry[3];
}
?>
    (function () {
        if (typeof (console) == 'object') {
            var url = "";
            if (window.response) {
                url = " [" + window.response.config.url + "]";
                window.response = undefined;
            }

            var data = <?php echo json_encode($items); ?>;
            var time = <?php echo $time; ?>;
            if (data.length > 0) {
                console.groupCollapsed("Stack Trace Report [" + Math.round(time * 1000) + "ms] [" + data.length + " Function Call]" + url + ":");
                data.forEach(function (item, i) {
                    console.groupCollapsed(item.text);
                    console.info("%c" + item.trace, 'font-size:11px;color:blue;');
                    console.groupEnd();
                });
                console.groupEnd();
            }
        }
    })();
</script>