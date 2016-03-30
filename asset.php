<?php
if (isset($_GET['p']) && isset($_GET['p'])) {
    header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60))); // 1 hour
    header('Cache-Control "max-age=3600, must-revalidate"');
    echo file_get_contents(@$_GET['e'] . @$_GET['p']);
}
?>