<?php

class Train {
    public $station;
    public $railway;
    public $rail_direction;
    public $time;

    public function __construct($station, $railway, $rail_direction, $time) {
        $this->station = $station;
        $this->railway = $railway;
        $this->rail_direction = $rail_direction;
        $this->time = $time;
    }
}
