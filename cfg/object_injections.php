<?php


use App\domain\{ProcedureMap, ProductMap};
use App\events\EventChannel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use App\domain\ProductMonthlyCounter;
use function DI\{create, get, factory};

return [

    //injections from app.cfg
    ProcedureMap::class => create()->constructor(get('app.procedure_map')),
    ProductMap::class => create()->constructor(get('app.product_map')),
    EntityManagerInterface::class => get(EntityManager::class),


    //factories

    //event channel - main place for communication between app components

    EventChannel::class => function ($c) {
        foreach ($c->get('app.subscribers') as $subscriber) {
            $subscribers[] = $c->get($subscriber);
        }
        return new EventChannel($subscribers ?? [], $c->get('app.observables'));
    },

    ProductMonthlyCounter::class => function ($c) {
        $dbLayer = $c->get($c->get('app.dbLayer'));
        $productMap = $c->get(ProductMap::class);
        $productCounter = ProductMonthlyCounter::creatAndAttachCountableProduct($dbLayer, $productMap);
        return $productCounter;
    },

    /* doctrine_orm_vendor_package */
    EntityManager::class => function ($c) {
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration($c->get('app.domain_path'), $c->get('app.dev_mode'));
        return EntityManager::create($c->get('app.database'), $doctrine_conf);
    },


];
