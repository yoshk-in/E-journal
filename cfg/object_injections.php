<?php

use App\base\{AbstractRequest, ConsoleRequest};

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use App\events\EventChannel;
use App\repository\{DoctrineORMAdapter,  ProductRepository};
use App\domain\{ProcedureMapManager, Product};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use function DI\{autowire, factory, get, create};

return [

    //injections from app.cfg
    ProcedureMapManager::class => create()->constructor(get('app.procedure_map')),
    EntityManagerInterface::class => get(EntityManager::class),
    AbstractRequest::class => get(ConsoleRequest::class),



    //factories
    EventChannel::class => function ($c) {
        foreach ($c->get('app.subscribers') as $subscriber) $subscribers[] = $c->get($subscriber);
        return new EventChannel($subscribers, $c->get('app.observables'));
    },
    /* doctrine_orm_vendor_package */
    EntityManager::class => function ($c) {
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration($c->get('app.domain_path'), $c->get('app.dev_mode'));
        return EntityManager::create($c->get('app.database'), $doctrine_conf);
    },

];
