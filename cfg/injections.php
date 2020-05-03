<?php

use App\domain\ProductMap;
use App\domain\procedures\{ProcedureMap};
use App\events\EventChannel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use App\domain\ProductMonthlyCounter;
use function DI\{create, get, factory};
use Psr\Container\ContainerInterface;
use App\events\IEventChannel;
use App\domain\procedures\factories\IProductProcedureFactory;
use App\domain\procedures\factories\ProcedureFactory;
use App\domain\productManager\ProductClassManager;


return [

    //injections from app.cfg
    ProcedureMap::class => create()->constructor(get('app.procedure_map')),
    ProductMap::class => create()->constructor(get('app.product_map')),
    EntityManagerInterface::class => get(EntityManager::class),
    IEventChannel::class => get(EventChannel::class),
    IProductProcedureFactory::class => get(ProcedureFactory::class),


    //factories


    ProductMonthlyCounter::class => function (ContainerInterface $c) {
        $dbLayer = $c->get($c->get('app.dbLayer'));
        $productMap = $c->get(ProductClassManager::class);
        return ProductMonthlyCounter::createAndAttachCountableProduct($dbLayer, $productMap);
    },

    /* doctrine_orm_vendor_package */
    EntityManager::class => function (ContainerInterface $c) {
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration($c->get('app.domain_path'), $c->get('app.dev_mode'));
        return EntityManager::create($c->get('app.database'), $doctrine_conf);
    },


];
