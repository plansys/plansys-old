<?php
/**
 * Description of JasperHelper
 *
 * @author Daniel Wendler
 */
namespace Jasper;


class JasperHelper {
    /*
     * Corrects path strings and removes slashes
     *
     * @param String $url path to correct
     * @return String
     */
    public static function url($url) {
        $url = str_replace('///', '/', $url);
        $url = str_replace('//',  '/', $url);
        $url = str_replace('//',  '/', $url);
        $url = str_replace('//',  '/', $url);
        if (substr($url, -1, 1) == '/') {
            $url = substr($url, 0, (strlen($url) - 1));
        }
        if ($url == '') {
            $url = '/';
        }
        return $url;
    }
}

