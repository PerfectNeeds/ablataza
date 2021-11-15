<?php

namespace MD\Utils;

class Date {

    public static $timezoneOffset;

    CONST DATE_FORMAT1 = 'Y-m-d H:i:s';
    CONST DATE_FORMAT2 = 'Y-m-d';
    CONST DATE_FORMAT3 = 'd/m/Y';
    CONST DATE_FORMAT4 = 'Y-m';
    CONST DATE_FORMAT5 = 'm/Y';
    CONST DATE_FORMAT6 = 'd/m/Y H:i A';
    CONST DATE_FORMAT_D = 'd';
    CONST DATE_FORMAT_M = 'm';
    CONST DATE_FORMAT_Y = 'Y';

    public function __construct() {
        self::open();
    }

    private static function open() { // This Functions for initialization
        return;
    }

    private static function setDefaultTimezone($country) {
        return date_default_timezone_set($country);
    }

    /**
     * Set Offset between GMT and
     * @param type $country
     * @return boolean
     */
    private static function setTimezoneOffset($country = null) {
        $origin_dtz = New \DateTimeZone($country);
        $remote_dtz = New \DateTimeZone("Etc/GMT");
        $origin_dt = New \DateTime("now", $origin_dtz);
        $remote_dt = New \DateTime("now", $remote_dtz);
        $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
        static::$timezoneOffset = $offset;
    }

    public function getTimezoneOffset() {
        return static::timezoneOffset;
    }

    /**
     *
     * @param type $year
     * @param type $month
     * @param type $day
     * @param type $timeFormat
     * @param type $separator
     * @return type
     */
    public static function getDatetimeNow($year = FALSE, $month = FALSE, $day = FALSE, $timeFormat = FALSE, $separator = '-') {
        self::open();

        if (!$year AND ! $month AND ! $day AND ! $timeFormat AND $separator == '-')
            return date(self::DATE_FORMAT1);


        if ($year AND $month AND $day)
            $date_string = self::DATE_FORMAT_Y . $separator . self::DATE_FORMAT_M . $separator . self::DATE_FORMAT_D;
        elseif ($year AND $month)
            $date_string = self::DATE_FORMAT_Y . $separator . self::DATE_FORMAT_M;
        elseif ($year AND ! self::DATE_FORMAT_M AND ! $day AND ! $timeFormat)
            $date_string = self::DATE_FORMAT_Y;
        elseif ($year AND ! $month AND ! $day AND ! $timeFormat)
            $date_string = self::DATE_FORMAT_Y;
        elseif (!$year AND $month AND ! $day AND ! $timeFormat)
            $date_string = self::DATE_FORMAT_M;
        elseif (!$year AND ! $month AND $day AND ! $timeFormat)
            $date_string = self::DATE_FORMAT_D;
        elseif (!$year AND $month AND $day AND ! $timeFormat)
            $date_string = self::DATE_FORMAT_M . $separator . self::DATE_FORMAT_D;
        elseif (!$year AND ! $month AND ! $day AND $timeFormat)
            $date_string = $timeFormat;
        else
            $date_string = self::DATE_FORMAT_Y . $separator . self::DATE_FORMAT_M . $separator . self::DATE_FORMAT_D . " " . $timeFormat;


        return date($date_string);
    }

    public static function createDateRangeArray($strDateFrom, $strDateTo) {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.
        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
        $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

        if ($iDateTo >= $iDateFrom) {
            array_push($aryRange, date(self::DATE_FORMAT2, $iDateFrom)); // first entry
            while ($iDateFrom < $iDateTo) {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange, date(self::DATE_FORMAT2, $iDateFrom));
            }
        }
        return $aryRange;
    }

    public static function getWeekNow() {

        switch (WEEK_START_DAY_NAME) {
            case 'Sat':
            case 'Saturday':
                $offset = "+2";
                break;
            case 'Sun':
            case 'Sunday':
                $offset = "+1";
                break;
            case 'Mon':
            case 'Monday':
                $offset = "0";
                break;
            case 'Tue':
            case 'Tuesday':
                $offset = "-1";
                break;
            case 'Wed':
            case 'Wednesday':
                $offset = "-2";
                break;
            case 'Thu':
            case 'Thursday':
                $offset = "-3";
                break;
            case 'Friday':
                $offset = "-4";
                break;
        }
        return date("W", strtotime("$offset day"));
    }

    public static function getMonthLenght($month = FALSE, $year = False) {
        $monthNum = ($month) ? ltrim($month, 0) : ltrim(self::getDatetimeNow(FALSE, TRUE), 0);

        $year = ($year) ? $year : self::getDatetimeNow(TRUE);

        $gregorian_leap_flag = !(($year) % 4);

        if (!$gregorian_leap_flag) {
            $gregorian_months_lenght = array
                (
                "1" => 31,
                "2" => 28,
                "3" => 31,
                "4" => 30,
                "5" => 31,
                "6" => 30,
                "7" => 31,
                "8" => 31,
                "9" => 30,
                "10" => 31,
                "11" => 30,
                "12" => 31
            );
        } else {
            $gregorian_months_lenght = array
                (
                "1" => 31,
                "2" => 29,
                "3" => 31,
                "4" => 30,
                "5" => 31,
                "6" => 30,
                "7" => 31,
                "8" => 31,
                "9" => 30,
                "10" => 31,
                "11" => 30,
                "12" => 31
            );
        }

        return $gregorian_months_lenght[$monthNum];
    }

    /**
     * @author Peter Nassef <peter.nassef@gmail.com>
     *
     * @param DateTime $first_date by this formate: 2013-01-12 05:08:16
     * @param DateTime $second_date by this formate: 2013-01-12 05:08:16
     * @return string Formatted interval string like Facebook.
     */
    public static function dateTimeDiffLikeFacebook($date) {

        if (empty($date)) {
            return "No date provided";
        }

        switch (Localization::SYSTEM_LANGUAGE) {
            case 'ar_EG':
                $periods = array("ثواني", "دقيقة", "ساعة", "يوم", "أسبوع", "شهر", "سنة", "عقد");
                break;
            case 'en_US':
                $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
                break;
        }
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");
        $now = strtotime(Date::getDatetimeNow());
        $unix_date = strtotime($date);
        // check validity of date
        if (empty($unix_date)) {
            return "Bad date";
        }
        // is it future date or past date
        if ($now > $unix_date) {
            $difference = $now - $unix_date;
            switch (Localization::SYSTEM_LANGUAGE) {
                case 'ar_EG':
                    $tense = "منذ";
                    break;
                case 'en_US':
                    $tense = "ago";
                    break;
            }
        } else {
            $difference = $unix_date - $now;
            $tense = "from now";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

//        if ($difference != 1) {
//            $periods[$j].= "s";
//        }
        switch (Localization::SYSTEM_LANGUAGE) {
            case 'ar_EG':
                return "{$tense} $difference $periods[$j]";
                break;
            case 'en_US':
                return "$difference $periods[$j] {$tense}";
                break;
        }
    }

    public static function addTodate($year = FALSE, $month = FALSE, $day = FALSE, $format = FALSE, $time = FALSE, $operation = FALSE) {
        $currentDate = self::getDatetimeNow();

        $operation = (!$operation) ? '+' : '-';
        if (!$format) {

            $time = ($time) ? ' h:i:s' : '';

            $format = self::DATE_FORMAT2 . $time;
        }

        if (is_numeric($year))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($currentDate)) . " " . $operation . $year . " years");

        if (is_numeric($month))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($currentDate)) . " " . $operation . $month . " month");

        if (is_numeric($day))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($day)) . " " . $operation . $day . " days");


        return date($format, $newDate);
    }

    public static function addDaysTodate($date, $year = FALSE, $month = FALSE, $day = FALSE, $format = FALSE, $time = FALSE, $operation = FALSE) {
        $operation = (!$operation) ? '+' : '-';
        if (!$format) {
            $time = ($time) ? ' h:i:s' : '';
            $format = self::DATE_FORMAT2 . $time;
        }

        if (is_numeric($year))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($date)) . " " . $operation . $year . " years");

        if (is_numeric($month))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($date)) . " " . $operation . $month . " month");

        if (is_numeric($day))
            $newDate = strtotime(date(self::DATE_FORMAT1, strtotime($date)) . " " . $operation . $day . " days");

        return date($format, $newDate);
    }

    /**
     * @author Peter Nassef <peter.nassef@gmail.com>
     *
     * @todo if get difference between tow dates
     *
     * @param type $date1
     * @param type $date2
     * @return type Days only not hour or minute
     */
    public static function dateDiffByDays($date1, $date2) {
        $ts1 = strtotime($date1);
        $ts2 = strtotime($date2);

        $seconds_diff = $ts2 - $ts1;

        return floor($seconds_diff / 3600 / 24);
    }

    /**
     * @author Peter Nassef <peter.nassef@gmail.com>
     *
     * @todo if current date bigger than expiry Date return false
     *
     * @param type $now
     * @param type $expiryDate
     * @return boolean
     */
    public static function dateDiff($now, $expiryDate) {
        $today = strtotime($now);
        $expiration_date = strtotime($expiryDate);

        if ($today > $expiration_date) {
            $valid = FALSE;
        } else {
            $valid = TRUE;
        }
        return $valid;
    }

    public static function getMonthNameByNumber($monthNum) {
        $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
        return $monthName; //output: May
    }

    /**
     * @author Peter Nassef <peter.nassef@gmail.com>
     *
     * @todo convert date from format to another format
     * @example Date::convertDateFormat('21/03/2010', 'd/m/Y', 'd-m-Y')
     * @return 21-03-2010
     * @param type $date
     * @param type $fromFormat
     * @param type $toFormat
     * @return type
     */
    public static function convertDateFormat($date, $fromFormat, $toFormat) {
        $date = trim($date);
        return $myDateTime = \DateTime::createFromFormat($fromFormat, $date)->format($toFormat);
    }

}

//Initialize the system date
new Date();
?>