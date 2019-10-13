<?php


use bootstrap\App;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;


require_once 'bootstrap/autoload_class.php';

$app_container = App::bootstrap();
$em = $app_container->get(EntityManager::class);
return ConsoleRunner::createHelperSet($em);





