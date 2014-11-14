<?php

function get_next($station, $direction, $time = NULL) {
    $now = date('H:i');
    $weekday = check_weekday();

    if (!isset($time)) {
        $time = time();
    }
    $times_list = get_time_tables($station, $direction);
    foreach ($times_list->{'odpt:' . $weekday} as $train) {
        var_dump($train);
        echo $train->{"odpt:departureTime"};
    }

    foreach ($times as $time) {
        $time_time = $time->{'odpt:departureTime'};
        if ($time_time < $now) {
            continue;
        }
        echo "[{$time_time}]";
        $params = explode(':', $time->{'odpt:destinationStation'});
        $params = explode('.', $params[1]);
        echo "{$params[1]}線: {$params[2]}";
        echo PHP_EOL;
    }

}

//usort($times, function($a, $b) {
//    return ($a->{'odpt:departureTime'} == $b->{'odpt:departureTime'} ? 0 : ($a->{'odpt:departureTime'} > $b->{'odpt:departureTime'} ? 1 : -1));
//});

function get_station_list($station) {
    $url_pref = 'https://api.tokyometroapp.jp/api/v2/';
    $url_api = 'datapoints';
    $url_query = "?rdf:type=odpt:Station&dc:title={$station}&acl:consumerKey=" . ACCESS_TOKEN;
    $url = $url_pref . $url_api . $url_query;
    $f = file_get_contents($url);
    return json_decode($f);
}

function get_time_tables($odpt_station, $direction = NULL) {
    $url_pref = 'https://api.tokyometroapp.jp/api/v2/';
    $url_api = 'datapoints';
    $url_query = '?rdf:type=odpt:StationTimetable&odpt:station=' . $odpt_station . '&acl:consumerKey=' . ACCESS_TOKEN;
    if (isset($data)) {
        $url_query .= '&odpt:RailDirection='. $direction;
    }
    $url = $url_pref . $url_api . $url_query;
    $json = file_get_contents($url);
    $arr = json_decode($json);
    return $arr[0];
}

function get_table_from_table($station) {
    $tmp = explode(':', $st->{'owl:sameAs'});
    $code = $tmp[1];
    $times = array();
    foreach (get_time_tables($code) as $table) {
        $times = array_merge($times, $table->{'odpt:weekdays'});
    }
    return $times;
}

