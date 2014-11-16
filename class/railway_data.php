<?php
class RailwayData {
    public $uid;
    public $name;
    public $url;
    public $stations;
    public $color;

    public function __construct($obj, $stations, $color) {
        $this->uid = $obj->{"@id"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->stations = $stations;
        $this->color = $color;
    }
}

