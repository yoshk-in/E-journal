<?php


namespace App\helpers;


use App\domain\procedures\ProcedureMap;
use App\domain\productManager\ProductClassManager;
use App\domain\ProductMap;
use App\repository\ProductRepository;
use Psr\Container\ContainerInterface;

trait TContainerGet
{
    protected ContainerInterface $container;


    protected function containerGet(string $class)
    {
        return $this->container->get($class);
    }

    protected function containerGets(array $classes): \Generator
    {
        foreach ($classes as $class) {
            yield $this->containerGet($class);
        }
    }
}