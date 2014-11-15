<?php
var_dump(json_decode(file_get_contents('./data/railways.json')));
exit;
$url_head = 'https://api.tokyometroapp.jp/api/v2/datapoints';
$url_foot = '?rdf:type=odpt:Railway&acl:consumerKey=4d710567d211e1bc54435bcc32b74ea757713ac0cfaa5741395b88edb12fcde7';
echo $url = $url_head . $url_foot;
$lines = json_decode(file_get_contents($url));
//var_dump($lines);
$railways = array();
foreach ($lines as $line) {
    $stations = array();
    foreach ($line->{"odpt:stationOrder"} as $st) {
        // sn station name
        $url_foot = '/' . $st->{"odpt:station"} . '?acl:consumerKey=4d710567d211e1bc54435bcc32b74ea757713ac0cfaa5741395b88edb12fcde7';
        $url = $url_head . $url_foot;
        $res = json_decode(file_get_contents($url));
        $station = $res[0];
        $stations[] = new StationData($station);
    }
    $railways[] = new RailwayData($line, $stations);
}
var_dump($railways);
echo json_encode($railways);

