<?php

namespace MD\Utils;

class Validate {
    /*
     * @version      : 1
     * @author       : Alex Seif <alex.seif@gmail.com>
     * Description   : function to test null for any type
     */

    public static function not_null($value) {

        if ($value == '0') {
            return FALSE;
        }
        if (is_array($value)) {
            if (sizeof($value) > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            if (($value != '') && (@strtolower($value) != 'null') && (@strlen(@trim($value)) > 0)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    public static function date($date, $format = 'DD/MM/YYYY') {
        if ($format == 'YYYY-MM-DD') {
            list($year, $month, $day) = explode('-', $date);
        }
        if ($format == 'YYYY/MM/DD') {
            list($year, $month, $day) = explode('/', $date);
        }
        if ($format == 'YYYY.MM.DD') {
            list($year, $month, $day) = explode('.', $date);
        }

        if ($format == 'DD-MM-YYYY') {
            list($day, $month, $year) = explode('-', $date);
        }
        if ($format == 'DD/MM/YYYY') {
            list($day, $month, $year) = explode('/', $date);
        }
        if ($format == 'DD.MM.YYYY') {
            list($day, $month, $year) = explode('.', $date);
        }

        if ($format == 'MM-DD-YYYY') {
            list($month, $day, $year) = explode('-', $date);
        }
        if ($format == 'MM/DD/YYYY') {
            list($month, $day, $year) = explode('/', $date);
        }
        if ($format == 'MM.DD.YYYY') {
            list($month, $day, $year) = explode('.', $date);
        }

        if (is_numeric($year) && is_numeric($month) && is_numeric($day)) {
            return checkdate($month, $day, $year);
        }
        return FALSE;
    }

    public static function email($email) {

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
