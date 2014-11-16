<?php
class TrainData {
    public $uid;
    public $url;
    public $train_number;
    public $railway;
    public $startingStation;
    public $terminalStation;
    public $fromStation;
    public $toStation;
    public $delay;

    public $locationFrom;
    public $locationTo;
    public $location;

    public $timeFrom;
    public $timeTo;

    public $remainTime;

    public static $type;

    public function __construct($obj) {
        $this->uid = $obj->{"@id"};
        $this->url = $obj->{"owl:sameAs"};
        $this->train_number = $obj->{"odpt:trainNumber"};
        $this->railway = $obj->{"odpt:railway"};
        $this->startingStation = $obj->{"odpt:startingStation"};
        $this->terminalStation = $obj->{"odpt:terminalStation"};
        $this->fromStation = $obj->{"odpt:fromStation"};
        $this->toStation = $obj->{"odpt:toStation"};
        $this->delay = $obj->{"odpt:delay"};
        if (TrainData::$type) {
            TrainData::$type = check_weekday();
        }
    }

    public function install($lib_location, $lib_timetables) {
        $this->locationFrom = $lib[$train->fromStation];
        $this->locationTo   = $lib[$train->toStation];
        $this->location     = $this->get_location($lib_location, $lib_timetables);
    }

    public function get_location($lib_location, $lib_timetables) {
        list($time_start, $time_end) = $this->get_times($lib_timetables);
        $now = float_time4(date('h:i'));
        $raito = time_progress_raito($time_start, $time_end, $now);
    }

    public function get_times($lib_timetables) {
        $now = float_time4(date('h:i'));
        $time_start = NULL;
        $time_end = NULL;
        foreach ($lib_timetables->{$railway}[TrainData::$type] as $time) {
            $time = float_time4($time);
            if ($now > $time) {
                continue;
            }
            if (!isset($time_start)) {
                $time_start = $time;
                continue;
            }
            $time_end = $time;
            break;
        }
        return array($time_start, $time_end);
    }

}

/*
@id 	URN 	固有識別子(ucode) 	◯
owl:sameAs 	URL 	固有識別子。命名ルールは、odpt.Train:TokyoMetro.路線名.列車番号である。 	◯
odpt:trainNumber 	xsd:string 	列車番号 	◯
odpt:railway 	odpt:Railway 	鉄道路線ID 	◯
odpt:startingStation 	odpt:Station 	列車の始発駅 	◯
odpt:terminalStation 	odpt:Station 	列車の終着駅 	◯
odpt:fromStation 	odpt:Station 	列車が出発した駅 	◯
odpt:toStation 	odpt:Station 	列車が向かっている駅

odpt:trainOwner     odpt:TrainOwner     車両の所属会社  ◯
odpt:delay  xsd:integer     遅延時間（秒）。5分未満は切り捨て。     ◯

 */
