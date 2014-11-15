<?php

class Train {
    public $station;
    public $railway;
    public $rail_direction;
    public $timestamp;

    public function __construct($station, $railway, $rail_direction, $timestamp) {
        $this->station = $station;
        $this->railway = $railway;
        $this->rail_direction = $rail_direction;
        $this->timestamp = $timestamp;
    }

    public function __tostring() {
        $text = "[" . date(DateTime::W3C, $this->timestamp) . "]";
        $text .= "{$this->railway}線: {$this->rail_direction}";
        return $text . PHP_EOL;
    }
}
