<?php
//var_dump(json_decode(file_get_contents('./data/railways.json')));
//exit;
require_once("../config/keys.php");
require_once("../class/railway_data.php");
require_once("../class/station_data.php");

$url_head = 'https://api.tokyometroapp.jp/api/v2/datapoints';
$url_foot = '?rdf:type=odpt:Train&acl:consumerKey=' . ACCESS_TOKEN;
echo $url = $url_head . $url_foot;
$lines = json_decode(file_get_contents($url));
var_dump($lines);
exit;
//var_dump($lines);
$railways = array();
foreach ($lines as $line) {
    $stations = array();
    foreach ($line->{"odpt:stationOrder"} as $st) {
        // sn station name
        $url_foot = '/' . $st->{"odpt:station"} . '?acl:consumerKey=' . ACCESS_TOKEN;
        $url = $url_head . $url_foot;
        $res = json_decode(file_get_contents($url));
        $station = $res[0];
        $stations[] = new StationData($station);
        var_dump($station);
        exit;
    }
    $railways[] = new RailwayData($line, $stations);
}
//echo json_encode($railways);

