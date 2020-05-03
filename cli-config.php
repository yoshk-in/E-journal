<?php


use bootstrap\App;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'bootstrap/autoload_class.php';
require_once 'public/productGenerating.php';
App::bootstrap(App::CLI);
$app_container = App::getContainer();
$em = $app_container->get(EntityManager::class);
return ConsoleRunner::createHelperSet($em);





