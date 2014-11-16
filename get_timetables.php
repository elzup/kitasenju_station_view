<?php

ini_set('memory_limit', '512M');

require_once('./config/keys.php');
require_once('./config/constants.php');
require_once('./helper/functions.php');
require_once('./class/railway_data.php');
require_once('./class/station_data.php');
require_once('./class/train_data.php');
require_once('./model/json.php');

$url_head = 'https://api.tokyometroapp.jp/api/v2/datapoints';

$railways = load_railways();
$params = array(
    'rdf:type' => 'odpt:StationTimetable',
    'acl:consumerKey' => ACCESS_TOKEN,
);
$url = $url_head . '?' . http_build_query($params);
$json = file_get_contents($url);
$stations = json_decode($json);
//var_dump($stations);
//echo "\n---------------\n";
$timetables = array();
foreach ($stations as $st) {
    $times = new stdclass();
    foreach (array(TIMETABLE_WEEKDAYS, TIMETABLE_HOLIDAYS, TIMETABLE_SATURDAYS) as $ts_type) {
        $times->{$ts_type} = array();
        foreach ($st->{'odpt:' . $ts_type} as $train) {
            var_dump($train);
            $time = $train->{"odpt:departureTime"};
            $trainType = $train->{"odpt:trainType"};
            if (!isset($times->{$ts_type}[$trainType])) {
                $times->{$ts_type}[$trainType] = array();
            }
            $times->{$ts_type}[$trainType][] = $time;
        }
    }
    $timetables[$st->{"odpt:station"}] = $times;
}
//var_dump($timetables);
echo json_encode($timetables);
