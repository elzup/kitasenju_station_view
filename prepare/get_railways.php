<?php
//var_dump(json_decode(file_get_contents('./data/railways.json')));
//exit;
require_once("../config/keys.php");
require_once("../class/railway_data.php");
require_once("../class/station_data.php");

$url_head = 'https://api.tokyometroapp.jp/api/v2/datapoints';
$url_foot = '?rdf:type=odpt:Railway&acl:consumerKey=' . ACCESS_TOKEN;
$url = $url_head . $url_foot;
$lines = json_decode(file_get_contents($url));
//var_dump($lines);
$railways = array();
$colorlib = explode(',', 'e60012,f39700,e60012,9caeb7,00a7db,009944,d7c447,9b7cb6,00ada9,bb641d');
foreach ($lines as $i => $line) {
    $stations = array();
    foreach ($line->{"odpt:stationOrder"} as $st) {
        // sn station name
        $url_foot = '/' . $st->{"odpt:station"} . '?acl:consumerKey=' . ACCESS_TOKEN;
        $url = $url_head . $url_foot;
        $res = json_decode(file_get_contents($url));
        $station = $res[0];
        $stations[] = new StationData($station);
    }
    $railways[] = new RailwayData($line, $stations, $colorlib[$i]);
}
echo json_encode($railways);

