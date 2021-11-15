<?php

namespace MD\Utils;

use MD\Utils\Slug;

class General {
    /*
     * @version      : 1
     * @author       : Peter Soliman <peter.samy@gmail.com>
     * Description   : function to Convert Array of Objects to One dimentional array
     */

    public static function objectsToArr($objectsARR, $varName) {
        $arrayVarValues = array();
        foreach ($objectsARR as $object) {
            array_push($arrayVarValues, $object->$varName);
        }
        return $arrayVarValues;
    }

    /*
     * @version      : 1
     * @author       : Peter Soliman <peter.samy@gmail.com>
     * Description   : checks if a given date is included in an interval
     */

    public static function dateInBetween($date, $startDate, $endDate) {
        if ((strtotime($date) - strtotime($startDate)) >= 0 && (strtotime($endDate) - strtotime($date)) >= 0)
            return TRUE;
        else
            return FALSE;
    }

    /*
     * @version      : 1
     * @author       : Peter Soliman <peter.samy@gmail.com>
     * Description   : checks if a given date is included in an interval
     */

    public static function slug($string) {
        return self::toAscii(trim($string));
    }

    /**
     *
     * @param string $str
     * @param array $replace
     * @param string $delimiter
     * @return string
     */
    public static function toAscii($str, $replace = array(), $delimiter = '-') {
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    /*
     * @version      : 1
     * @author       : Peter Boshra <peteracmilan@gmail.com>
     * Description   : checks if a given date is included in an interval
     * attributes    : DateTime  objects  
     */

    public static function dateDiffrence($startDate, $endDate) {
        return $endDate - $startDate;
    }

    /*
     * @version      : 1
     * @author       : Peter Boshra <peteracmilan@gmail.com>
     * Description   :return string like that peter-boshra
     * attributes    :string  
     */

    public static function seoUrl($string) {
        return Slug::sanitize($string);
//        //lower case everything
//        $string = strtolower($string);
//        //make alphaunermic
//        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
//        //Clean multiple dashes or whitespaces
//        $string = preg_replace("/[\s-]+/", " ", $string);
//        //Convert whitespaces and underscore to dash
//        $string = preg_replace("/[\s_]/", "-", $string);
//        return $string;
    }

    /*
     * @version      : 1
     * @author       : Bishoy Yahya <bishocis@gmail.com>
     * Description   :return months witn 2 langauages
     * attributes    :string  
     */

    public static function yearMonths() {
        $months = array(
            1 => array("Ar" => "يناير", "En" => "January"),
            2 => array("Ar" => "فبراير", "En" => "February"),
            3 => array("Ar" => "مارس", "En" => "March"),
            4 => array("Ar" => "ابريل", "En" => "April"),
            5 => array("Ar" => "مايو", "En" => "May"),
            6 => array("Ar" => "يونيو", "En" => "June"),
            7 => array("Ar" => "يوليو", "En" => "July"),
            8 => array("Ar" => "اغسطس", "En" => "August"),
            9 => array("Ar" => "سبتمبر", "En" => "September"),
            10 => array("Ar" => "اكتوبر", "En" => "October"),
            11 => array("Ar" => "نوفمبر", "En" => "November"),
            12 => array("Ar" => "ديسيمبر", "En" => "December"),
        );
        return $months;
    }

}
