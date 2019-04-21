<?php

namespace doctrine\cli_config;

require_once 'bootstrap/bootstrap.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet(\App\base\AppHelper::getEntityManager());
