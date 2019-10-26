<?php


use App\domain\{ProcedureMap};
use App\events\EventChannel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use function DI\{create, get};

return [

    //injections from app.cfg
    ProcedureMap::class => create()->constructor(get('app.procedure_map')),
    EntityManagerInterface::class => get(EntityManager::class),


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
