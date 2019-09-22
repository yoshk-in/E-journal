<?php

namespace bootstrap;


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;


class AppContainer
{
    static public function bootstrap(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions('cfg/database.php');
        $builder->addDefinitions('cfg/procedure_map.php');
        $builder->addDefinitions('cfg/app.php');
        $builder->addDefinitions('cfg/object_injections.php');

        $container = $builder->build();
        return $container;
    }

}