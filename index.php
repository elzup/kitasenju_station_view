<?php

require_once('./config/keys.php');
require_once('./config/constants.php');
require_once('./helper/functions.php');
require_once('./class/train.php');

require_once('./controller/get_next.php');

get_future_trains('北千住');
