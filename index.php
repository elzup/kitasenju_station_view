<?php

require_once('./config/keys.php');
require_once('./config/constants.php');
require_once('./helper/functions.php');
require_once('./class/train.php');
require_once('./class/railway_data.php');
require_once('./class/station_data.php');
require_once('./model/json.php');

require_once('./controller/get_next.php');

$railways = load_railways();

// 東京中心
$lat = 35.6925207;
$lon = 139.7821457;
$zoom = 12;
$maptype = "ROADMAP";

$locs = array();

//foreach ($tweets as $i => $st) { 
//    if (!isset($st->geo)) continue;
//    $locs[] = '["' . escape_js_string($st->text) . '", ' . $st->geo->coordinates[0] . ', ' . $st->geo->coordinates[1] . ']';
//}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>Railway Map</title>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&sensor=TRUE"></script>
</head>
<body onload="initialize()">
<script type="text/javascript">

function initialize() {
    var mapOptions = {
    center: new google.maps.LatLng(<?= $lat . ', ' . $lon ?>), zoom: <?= $zoom ?>,
        mapTypeId: google.maps.MapTypeId.<?= $maptype ?>
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    var infowindow = new google.maps.InfoWindow();
    var geocoder = new google.maps.Geocoder();

    var railways = <?= json_encode($railways) ?>;
    console.log(railways);

    
    for (var k = 0; k < railways.length; k++) {
        var railway = railways[k];
        var pre_loc = "";
        for (var j = 0; j < railway.stations.length; j++) {
            var st = railway.stations[j];
            var lat = st.location.lat;
            var lon = st.location.lon;
    
            var col = '#ff0000';
            set_marker(col, lat, lon, map, infowindow, "test");
            if (!pre_loc) {
                var points = [
                    new google.maps.LatLng(pre_loc.lat, pre_loc.lon),
                    new google.maps.LatLng(lat, lon)
                ];
                var flightPath = new google.maps.Polyline({
                    path: points,
                    geodesic: true,
                    strokeColor: "#" + col,
                    strokeOpacity: 1.0,
                    strokeWeight: 5
                });
                flightPath.setMap(map);
            }
            pre_loc = st.location;
        }
    }
    set_marker("FFAA00", <?= $lat ?>, <?= $lon ?>, map, infowindow, "画面中央");
    console.log("end");
}

function set_marker(col, lat, lon, map, infowindow, text) {
    var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + col,
        new google.maps.Size(21, 34),
            new google.maps.Point(0, 0),
            new google.maps.Point(10, 34));
    marker = new google.maps.Marker({
    position: new google.maps.LatLng(lat, lon),
        icon: pinImage,
        map: map
    });
    google.maps.event.addListener(marker, 'mouseover', (function(marker, user_lock, k, j) {
        return function() {
            infowindow.setContent(text);
            infowindow.open(map, marker);
        }
    })(marker));
}

</script>
<div id="wrapper">
    <h1>Railway Map</h1>
</div>
<div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>
