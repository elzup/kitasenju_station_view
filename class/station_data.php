<?php
class StationData {
    public $uid;
    public $ja_name;
    public $name;
    public $url;
    public $location;

    public function __construct($obj) {
        var_dump($obj);
        $this->uid = $obj->{"@id"};
        $this->ja_name = $obj->{"dc:title"};
        $this->name = $obj->{"dc:title"};
        $this->url = $obj->{"owl:sameAs"};
        $this->location = new stdclass();
        $this->location->lat = $obj->{"geo:lat"};
        $this->location->lon = $obj->{"geo:long"};
    }
}
