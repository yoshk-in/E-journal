<?php

namespace bootstrap;


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;


class AppContainer
{
    static public function bootstrap(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('bootstrap/cfg/database.php');
        $builder->addDefinitions('bootstrap/cfg/procedure_map.php');
        $builder->addDefinitions('bootstrap/cfg/app.php');
        $builder->addDefinitions('bootstrap/cfg/object_injections.php');

        $container = $builder->build();
        return $container;
    }

}