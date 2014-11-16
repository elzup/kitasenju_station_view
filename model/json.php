<?php

function load_railways() {
    return json_decode(file_get_contents('./data/railways.json'));
}

function load_lib_location() {
    return json_decode(file_get_contents('./data/lib_location.json'));
}

function load_lib_timetable() {
    return json_decode(file_get_contents('./data/lib_timetable2.json'));
}

function load_linemark_chars() {
    return str_split('MGMHTCYZNF');
}

function load_filenames_linemark() {
    $urls = scandir('./images/line_mark/');
    unset($urls[0]);
    unset($urls[1]);
    return $urls;
}

