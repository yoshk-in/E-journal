<?php

namespace tests;

use \App\domain\G9;


require_once 'bootstrapTests.php';

$g9 = new G9(120051);
$g9->nextProcedure();

$g9->nextProcedure();

$g9->nextProcedure();
var_dump($g9);
