<?php

function load_trains() {
    $url_head = 'https://api.tokyometroapp.jp/api/v2/datapoints';
    $url_foot = '?rdf:type=odpt:Train&acl:consumerKey=' . ACCESS_TOKEN;
    $url = $url_head . $url_foot;
    $trains = json_decode(file_get_contents($url));
    $train_list = array();
    foreach ($trains as $train) {
        $train_list[] = new TrainData($train);
    }
    return $train_list;
}

function get_future_trains($station_name) {
    $station_list = get_station_list($station_name);
    $weekday = check_weekday();
    $weekday_tommorow = check_weekday(strtotime("+1day"));
    $trains = array();
    foreach ($station_list as $st) {
        $station = get_colon_value($st->{"owl:sameAs"});
        $railway = get_colon_value($st->{"odpt:railway"});
        $times = get_table_from_station($st);
        foreach ($times->{'odpt:' . $weekday} as $time) {
            $trains[] = new Train($station, $railway, get_colon_value($time->{"odpt:destinationStation"}), strtotime($time->{"odpt:departureTime"}));
        }
        foreach ($times->{'odpt:' . $weekday_tommorow} as $time) {
            $trains[] = new Train($station, $railway, get_colon_value($time->{"odpt:destinationStation"}), strtotime($time->{"odpt:departureTime"} . ' + 1day'));
        }
    }
    $trains = sort_trains_by_time($trains);
    return $trains;
}

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

function sort_trains_by_time(array $trains) {
    usort($trains, function($a, $b) {
        return ($a->timestamp == $b->timestamp ? 0 : ($a->timestamp > $b->timestamp ? 1 : -1));
    });
    return $trains;
}

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

function get_table_from_station($station) {
    $tmp = explode(':', $station->{'owl:sameAs'});
    $code = $tmp[1];
    return get_time_tables($code);
}

