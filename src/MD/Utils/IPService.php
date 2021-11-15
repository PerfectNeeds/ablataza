<?php

namespace MD\Utils;

class IPService {

    public static function getIp() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function getIPLocation($ip = NULL) {
        if ($ip == NULL) {
            $ip = self::getIp();
        }
        return json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
    }

}

?>