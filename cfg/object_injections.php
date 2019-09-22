<?php

use App\base\AbstractRequest;
use App\base\ConsoleRequest;
use App\cache\Cache;
use App\console\NumbersParser;
use Doctrine\ORM\Tools\Setup;
use App\domain\{EventChannel, ORM, ProcedureMapManager, Product, ProductRepository};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use function DI\{autowire, factory, get, create};

return [

    //injections from app.cfg
    ProcedureMapManager::class => create()->constructor(get('app.procedure_map')),
    EntityManagerInterface::class => get(EntityManager::class),
    ORM::class => autowire()->constructorParameter('domainClass', get('app.domain_class')),
    AbstractRequest::class => get(ConsoleRequest::class),



    //factories
    EventChannel::class => function ($c) {
        foreach ($c->get('app.subscribers') as $subscriber) $subscribers[] = $c->get($subscriber);
        return new EventChannel($subscribers, $c->get('app.observables'));
    },

    EntityManager::class => function ($c) {
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration($c->get('app.domain_path'), $c->get('app.dev_mode'));
        return EntityManager::create($c->get('app.database'), $doctrine_conf);
    },
];
