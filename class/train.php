<?php

class Train {
    public $uid;
    public $station;
    public $railway;
    public $rail_direction;

    public function __construct($uid, $station, $rail_direction) {
        $this->uid = $uid;
        $this->station = $station;
        $this->railway = $railway;
        $this->rail_direction = $rail_direction;
    }
}
