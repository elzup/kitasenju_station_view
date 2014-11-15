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

class StationData {
    public $uid;
    public $name;
    public $url;
    public $lat;
    public $lon;

    public function __construct($obj) {
        $this->uid = $obj->{"@id"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->lat = $obj->{"geo:lat"};
        $this->lon = $obj->{"geo:long"};
    }
}
