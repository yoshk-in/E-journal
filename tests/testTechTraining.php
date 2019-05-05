<?php

namespace tests;

require_once 'bootstrapTests.php';

$g9 = new \App\domain\G9(120051);
$g9->nextProcedure();
$g9->endProcedure();
$g9->nextProcedure();
$g9->nextTraining('vibro');

var_dump($g9);
