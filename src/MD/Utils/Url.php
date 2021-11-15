<?php

namespace MD\Utils;

class Url {
    /*
     * @version      : 1
     * @author       : Peter Nassef <peter.nassef@gmail.com>
     * @Description  : return youtube thumbnail
     * @param        : type $youtubeUrl
     */

    public static function youtubeThumbnail($youtubeUrl) {
        $params = array();
        $queryString = parse_url($youtubeUrl, PHP_URL_QUERY);
        parse_str($queryString, $params);
        return 'http://img.youtube.com/vi/' . $params['v'] . '/default.jpg';
//        return 'http://img.youtube.com/vi/' . $params['v'] . '/0.jpg';
    }

    public static function addGetParamToUrl($url, $varName, $value) {
        // is there already an ?
        if (strpos($url, "?")) {
            $url .= "&" . $varName . "=" . $value;
        } else {
            $url .= "?" . $varName . "=" . $value;
        }
        return $url;
    }

    public static function curPageURL() {
        $pageURL = 'http';
//        if ($_SERVER["HTTPS"] == "on") {
//            $pageURL .= "s";
//        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public static function addGetLastParamInUrl($url = FALSE) {
        if ($url == FALSE) {
            $url = self::curPageURL();
        }

        if (stripos($url, "?") != FALSE) {
            $url = substr($url, 0, stripos($url, "?"));
        }
        $url = explode('/', $url);
        if (empty($url[count($url) - 1])) {
            if (!empty($url[count($url) - 2])) {
                return $url[count($url) - 2];
            }
        } else {
            return $url[count($url) - 1];
        }
    }

}
