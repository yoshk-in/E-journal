<?php



require_once 'bootstrap/autoload_class.php';

use bootstrap\App;

App::bootstrap(App::CLI);
$productClassMng = App::getContainer()->make(\App\domain\productManager\ProductClassManager::class, ['checkEnvConf' => false]);
$productClassMng->generateProductClasses();


