<?php

function generate_location_lib($railways) {
    $lib = array();
    foreach($railways as $rail) {
        foreach ($rail->stations as $st) {
            $lib[$st->url] = $st->location;
        }
    }
    return $lib;
}

function install_train(&$trains, $lib_location) {
    foreach ($trains as $key => &$train) {
        if (!$train->toStation) {
            unset($trains[$key]);
            continue;
        }
        $train->install($lib_location, $lib_timetables);
    }
}

// 何割終わっているか
function time_progress_raito($time_start, $time_end, $time) {
    list($hs, $is) = explode(':', $time_start);
    list($he, $ie) = explode(':', $time_end);
    list($ht, $it) = explode(':', $time);
    $start = $hs * 60 + $is;
    $end   = $he * 60 + $ie;
    $p   = $ht * 60 + $it;
    echo $diff = $end - $start;
    echo $pdiff = $p - $start;
    return $pdiff / $diff;
}

function calc_location($location_start, $location_end, $raito) {
    $loc = new stdclass();
    $loc->lat = $location_end->lat * $raito + (1 - $raito) * $location_start->lat;
    $loc->lon = $location_end->lon * $raito + (1 - $raito) * $location_start->lon;
    return $loc;
}

function float_time4($time) {
    if ($time < '03:00') {
        list($h, $i) = explode(':', $time);
        $h += 24;
        $time = implode(':', array($h, $i));
        return $time;
    }
} 

function check_weekday($timestamp = NULL) {
    if (!isset($timestamp)) {
        $timestamp = time();
    }
    $ymd = split_ymd($timestamp);
    if (is_holiday($ymd[0], $ymd[1], $ymd[2])) {
        return TIMETABLE_HOLIDAYS;
    } elseif (is_saturday()) {
        return TIMETABLE_SATURDAYS;
    }
    return TIMETABLE_WEEKDAYS;
}

function split_ymd($timestamp = NULL) {
    if (!isset($timestamp)) {
        $timestamp = time();
    }
    return explode(':', date('Y:m:d', $timestamp));
}

function is_holiday($year, $month, $day) {
    if ( date("w", mktime(0,0,0, $month ,$day ,$year )) == 0 ) {
        return TRUE;
    }
    $known_hol = array("1/1", "4/29", "5/3", "5/4", "5/5", "11/3", "11/23", "12/23");
    if ( $year > 1999 ) {
        $y = $year - 2000;
        $spring_equinox   = (int)(20.69115 + 0.2421904 * $y - (int)($y/4 + $y/100 + $y/400) );
        $autumnal_equinox = (int)(23.09000 + 0.2421904 * $y - (int)($y/4 + $y/100 + $y/400) );
        array_push( $known_hol, "3/".$spring_equinox );
        array_push( $known_hol, "9/".$autumnal_equinox );
    }
    if( array_search( $month."/".$day , $known_hol ) !== FALSE )
        return TRUE;
    $pre_day = $day - 1;
    if( $pre_day < 1 )
        return FALSE;
    if( date("w", mktime(0,0,0, $month ,$pre_day ,$year ) ) == 0 ) {
        if( array_search( $month."/".$pre_day , $known_hol ) !== FALSE )
            return TRUE;
    }
    for( $tom = 1; $tom < 8; $tom++ ) {
        if( date("w", mktime(0,0,0, $month ,$tom ,$year )) == 1 )
            break;
    }
    if( ($month == 10) || ($month == 1) ) {
        if( $day == ($tom+7) )
            return TRUE;
    }
    if( ($month == 7) || ($month == 9) ) {
        if( $day == ($tom+14) )
            return TRUE;
    }
    return FALSE;
}

function is_sunday($timestamp = NULL) {
    $ymd = split_ymd($timestamp);
    return date("w", mktime(0,0,0, $ymd[1] ,$ymd[2], $ymd[0])) == 0;
}

function is_saturday($timestamp = NULL) {
    $ymd = split_ymd($timestamp);
    return date("w", mktime(0,0,0, $ymd[1] ,$ymd[2], $ymd[0])) == 6;
}

function get_colon_value($text) {
    $arr = explode(':', $text);
    return $arr[1];
}
