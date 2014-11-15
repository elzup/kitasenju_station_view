<?php

function load_railways() {
    return json_decode(file_get_contents('./data/railways.json'));
}
