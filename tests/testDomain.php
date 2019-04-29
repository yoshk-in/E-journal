<?php

namespace tests;

use \App\domain\G9;
use \App\domain\G9StatesList;

require_once 'vendor/autoload.php';

$g9 = new G9(120051);
$statesList = new G9StatesList($g9);
$g9->setStatesList($statesList);

var_dump($g9);
