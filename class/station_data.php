<?php
class StationData {
    public $uid;
    public $name;
    public $url;
    public $location;

    public function __construct($obj) {
        $this->uid = $obj->{"@id"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->location = new stdclass();
        $this->location->lat = $obj->{"geo:lat"};
        $this->location->lon = $obj->{"geo:long"};
    }
}
