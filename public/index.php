<?php

namespace pub;

use bootstrap\AppContainer;
use \App\controller\Controller;

require_once 'bootstrap/autoload_class.php';


$container = AppContainer::bootstrap();
$controller = $container->get(Controller::class);
$controller->handleConsoleRequest();
