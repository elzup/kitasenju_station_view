<?php

require_once("./keys.php");
$name = '北千住';
$url = 'https://api.tokyometroapp.jp/api/v2/places/ucode_00001C000000000000010000030C4406&acl:consumerKey='. ACCESS_TOKEN;
$list = get_station_list($name);
$times = array();



$now = date('H:i');

foreach ($list as $st) {
    $code = explode(':', $st->{'owl:sameAs'})[1];
    foreach (get_time_tables($code) as $table) {
        $times = array_merge($times, $table->{'odpt:weekdays'});
    }
}

usort($times, function($a, $b) {
    return ($a->{'odpt:departureTime'} == $b->{'odpt:departureTime'} ? 0 : ($a->{'odpt:departureTime'} > $b->{'odpt:departureTime'} ? 1 : -1));
});

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

function get_station_list($station) {
    $url_pref = 'https://api.tokyometroapp.jp/api/v2/';
    $url_api = 'datapoints';
    $url_query = "?rdf:type=odpt:Station&dc:title={$station}&acl:consumerKey=" . ACCESS_TOKEN;
    $url = $url_pref . $url_api . $url_query;
    $f = file_get_contents($url);
    return json_decode($f);
}

function get_time_tables($odpt_station)
{
    $url_pref = 'https://api.tokyometroapp.jp/api/v2/';
    $url_api = 'datapoints';
    $url_query = '?rdf:type=odpt:StationTimetable&odpt:station=' . $odpt_station . '&acl:consumerKey=' . ACCESS_TOKEN;
    $url = $url_pref . $url_api . $url_query;
    $f = file_get_contents($url);
    return json_decode($f);
}

exit;

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<?php
?>
</body>
</html>
