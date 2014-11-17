<?php

require_once('./config/keys.php');
require_once('./config/constants.php');
require_once('./helper/functions.php');
require_once('./class/railway_data.php');
require_once('./class/station_data.php');
require_once('./class/train_data.php');
require_once('./model/json.php');

require_once('./controller/get_next.php');

ini_set('display_errors', '1');
error_reporting(E_ALL);

$target_rail = @$_GET['rail'];
$target_rail_name = NULL;

$railways = load_railways();
$lib_location = load_lib_location();
$lib_timetables = load_lib_timetable();
$lib_color = array();
$linemark_chars = load_linemark_chars();
foreach ($railways as $i => &$rail) {
    $lib_color[$rail->url] = $rail->color;
    if ($target_rail && $target_rail != $linemark_chars[$i]) {
        unset($railways[$i]);
        continue;
    }
    $rail->char = $linemark_chars[$i];
    if (isset($target_rail) && $rail->char == $target_rail) {
        $target_rail_name = $rail->url;
    }
}
$railways = array_values($railways);
unset($linemark_chars[2]);

$trains = load_trains();
install_train($trains, $lib_location, $lib_timetables, $lib_color);

if (isset($target_rail_name)) {
    foreach($trains as $key => $train) {
        if ($train->railway != $target_rail_name) {
            unset($trains[$key]);
        }
    }
    $trains = array_values($trains);
}
//var_dump($trains);

// 東京中心
$lat =  35.6925207;
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
    <title>東京メトロ RailMap</title>
<link rel="stylesheet" href="./style.css">
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&sensor=TRUE"></script>
<script>

var map;
var infowindow;
var geocoder;
function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(<?= $lat . ',' . $lon ?>),
        zoom: <?= $zoom ?>,
        mapTypeId: google.maps.MapTypeId.<?= $maptype ?>
    };
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
    infowindow = new google.maps.InfoWindow({
        pixelOffset: new google.maps.Size(-25, 0)
    });
    geocoder = new google.maps.Geocoder();

    var railways = <?= json_encode($railways) ?>;
    console.log(railways);
    var trains = <?= json_encode($trains) ?>;
    console.log(trains);

    // set station markers
    for (var k = 0; k < railways.length; k++) {
        var railway = railways[k];
        var col = railway.color;
        var pre_loc = "";
        for (var j = 0; j < railway.stations.length; j++) {
            var st = railway.stations[j];
            var lat = st.location.lat;
            var lon = st.location.lon;
    
            set_marker_station(col, lat, lon, map, infowindow, st.name, st.code);
            if (pre_loc) {
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
            pre_station = st.name;
            pre_loc = st.location;
        }
    }

    // set train location markers
    console.log("manage trains");
    console.log(trains.length);
    for (var k = 0; k < trains.length; k++) {
        var train = trains[k];
        if (!train) {
            continue;
        }
        set_marker_train(train, map, infowindow);
    }

//    set_marker("FFAA00", <?= $lat ?>, <?= $lon ?>, map, infowindow, "画面中央");
    console.log("end");
}

function animateCircle() {
    var count = 0;
    setInterval(function() { count = (count + 1) % 300;
    var icons = line.get('icons');
    icons[0].offset = (count / 3) + '%';
    line.set('icons', icons);
    }, 20);
}

function set_marker_station(col, lat, lon, map, infowindow, text, code) {
    var img_path = "<?= PATH_STATION_ICON ?>" + code + '.png';
    var pinImage = new google.maps.MarkerImage(
        img_path,
        new google.maps.Size(68, 68),
        new google.maps.Point(0, 0),
        new google.maps.Point(10, 10),
        new google.maps.Size(20, 20)

    );
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

function set_marker_train(train, map, infowindow) {
    //var img_path = "images/allow.png";
    var img_path = "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + train.color;
//        new google.maps.Size(40, 40),
//        new google.maps.Point(0, 0),
//        new google.maps.Point(10, 10),
//        new google.maps.Size(20, 20)
    var pinImage = new google.maps.MarkerImage(
        img_path,

        new google.maps.Size(21, 34),
        new google.maps.Point(0,0),
        new google.maps.Point(10, 34)
    );
    marker = new google.maps.Marker({

    position: new google.maps.LatLng(train.location.lat, train.location.lon),
        icon: pinImage,
        map: map
    });
//アニメーション開始
}

google.maps.event.addDomListener(window, 'load', initialize);

$(function() {
    $helpdiv = $('.help-div');
    $helpdiv.hide();
    $('.help').click(function() {
        if ($helpdiv.css('display') == 'none') {
            $helpdiv.slideDown();
        } else {
            $helpdiv.slideUp();
        }
    });
});

</script>
</head>
<body>
<div id="wrapper">
    <header>
        <a href="./"><h1>東京メトロ Railway Map</h1></a>
    </header>
    <div class="row">
        <div class="description">
            <p>東京メトロで走っている電車を表示します</p>
            <p><?php echo date('Y年m月d日 H時i分') ?> 現在走っている電車は <?= count($trains) ?> 車です</p>
            <p class="help open"><span>ヘルプ</span></p>
            <div class="help-div">
                <ul>
                    <li>運行中の電車にマーカーが表示されています</li>
                    <li>駅のマークにマウスをのせると駅名が表示されます</li>
                    <li>右のラインマークをクリックするとフィルターを書けれます</li>
                    <li>フィルターを消したい場合はタイトルリンクをクリック</li>
                    <li>GoogleMapの操作方法と同じです</li>
                </ul>
            <p class="help close"><span>閉じる</span></p>
            </div>
        </div>
        <div id="controllers">
            <?php foreach ($linemark_chars as $c) { ?>
                <a href="?rail=<?= $c ?>"><img class="linemark<?= $target_rail == $c ? " spot" : "" ?>" src="<?= PATH_LINE_MARK . $c . '.jpg' ?>" alt=""></a>
            <?php }?>
        </div>
    </div>
</div>
<div id="map_canvas" style="margin: 0 auto; width:80%; height:450px"></div>
</body>
</html>
