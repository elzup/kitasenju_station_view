<?php
class RailwayData {
    public $uid;
    public $name;
    public $url;
    public $stations;

    public function __construct($obj, $stations) {
        $this->uid = $obj->{"@id"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->stations = $stations;
    }
}

