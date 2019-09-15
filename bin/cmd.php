<?php


use App\domain\PartialProcedure;
use App\domain\Procedure;
use App\domain\Product;
use bootstrap\AppContainer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;



require_once 'bootstrap/autoload_class.php';




function methods($command) : array
{
    $drop = 'dropDatabase';
    $create = "createSchema";
    $aliases = [
        'drop' => [$drop],
        'create' => [$create],
        'update' => [$drop, $create]
    ];

    return $aliases[$command];
}



$container = AppContainer::bootstrap();


list($file, $command) = $_SERVER['argv'];

$em = $container->get(EntityManager::class);
$cmdTool = new SchemaTool($em);
$domain_classes = [Product::class, Procedure::class, PartialProcedure::class];
foreach ($domain_classes as $class) {
    $metadata_classes[] = $em->getClassMetadata($class);
}

$methods = methods($command);

foreach ($methods as $method) {
    if ($method = 'createSchema') $args = $metadata_classes;
    $cmdTool->$method($args ?? null);
}

