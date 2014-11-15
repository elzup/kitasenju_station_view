<?php

require_once('./config/keys.php');
require_once('./config/constants.php');
require_once('./helper/functions.php');
require_once('./class/train.php');

require_once('./controller/get_next.php');

$trains = get_future_trains('北千住');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <title>北千住 リアルタイムホーム</title>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
<script>
var train_list = <?= json_encode($trains) ?>;
console.log(train_list);

var line_train_lists = [];
for (var i = 0; i < train_list.length; i++) {
    var tr = train_list[i];
    if (!line_train_lists[tr.rail_direction]) {
        line_train_lists[tr.rail_direction] = [];
    }
    line_train_lists[tr.rail_direction].push(tr);
}

for (var rail in line_train_lists) {
    var train_list = line_train_lists[rail];
    $('#trainsbox').append($('<div/>').addClass(rail)).html(rail);

    for (var i = 0; i < train_list.length; i++) {
        var tr = train_list[i];
        var diff = getDiff(tr);
        if (diff < 0) {
            console.log('s');
            continue;
        }
        console.log("next: " + diff);
        setTimeout("setTrain(\"" + rail + "\", " + i + ")", diff * 1000);
        break;
    }
}

function createCountdownTimer(target, time) {
    $timer = $('<div/>').attr('data-count', target).addClass('timer').html(time);
    $(target).append($timer);
    
    setTimeout('countDown(' + target + ')', Math.round(time / 1000));
}

function countDown(target) {
    var $timer = $('[data-count=' + target + ']');
    var time = $timer.html();
    time--;
    if (time == 0) {
        $timer.parent().removeChild('.timer');
    }
    $timer.html(time);
    setTimeout('countDown(' + target + ')', 1000);
}

// return ms
function getDiff(tr) {
    var now = Math.round(new Date().getTime() / 1000);
    return (tr.timestamp - now) * 1000;
}

function setTrain(rail, i) {
    var train_list = line_train_lists[rail];
    console.log("check: ");
    if (!train_list[i]) {
        'err';
    }
    appendTrain(train_list[i]);
    if (!train_list[i + 1]) {
        return;
    }
    // set next trigger
    var diff = getDiff(train_list[i + 1]);
    var uid = tr.railway + tr.timestamp;
    createCountdownTimer(uid, diff);
    setTimeout("setTrain(\"" + rail + "\", " + (i + 1) + ")", diff * 1000);
}

function appendTrain(tr) {
    // set train[i]
    var date = new Date();
    date.setTime(tr.timestamp);
    var text = "[" + date.getFullYear() + ":" + date.getMonth() + ":" + date.getDate() + "]" + tr.railway;
    // generate train[i] element
    var uid = tr.railway + tr.timestamp;
    $trainDiv = $('<div/>').addClass('train').attr('id', uid).html(text);
    $('#trainsbox').append($trainDiv);
}

</script>
<header></header>
<div id="wrapper">
    <h1>北千住 リアルタイムホーム</h1>
        <div id="trainsbox">
        </div>
    </div>
<footer></footer>
</body>
</html>
