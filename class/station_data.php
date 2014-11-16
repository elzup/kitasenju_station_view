<?php
class StationData {
    public $uid;
    public $ja_name;
    public $name;
    public $url;
    public $location;
    public $code;

    public function __construct($obj) {
        $this->uid = $obj->{"@id"};
        $this->ja_name = $obj->{"dc:title"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->code = $obj->{"odpt:stationCode"};
        $this->location = new stdclass();
        $this->location->lat = $obj->{"geo:lat"};
        $this->location->lon = $obj->{"geo:long"};
    }
}
