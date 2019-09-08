<?php

use App\base\AbstractRequest;
use App\base\ConsoleRequest;
use App\command\Command;
use Doctrine\ORM\Tools\Setup;
use App\domain\{EventChannel, ORM, ProcedureMap, Product, ProductRepository};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use function DI\{autowire, get, create};

return [
    EntityManagerInterface::class => function ($c) {
        $doctrine_conf = Setup::createAnnotationMetadataConfiguration($c->get('app.domain_path'), $c->get('app.dev_mode'));
        return EntityManager::create($c->get('app.database'), $doctrine_conf);
    },
    ORM::class => autowire()->constructorParameter('domainClass', get('app.domain_class')),
    AbstractRequest::class => function ($c) {
        return $c->get(ConsoleRequest::class);
    },
    EventChannel::class => function ($c) {
        foreach ($c->get('app.procedure_subscribers') as $subscriber) {
            $subs[] = $c->get($subscriber);
        }
        return new EventChannel($subs, $c->get('app.observables'));
    }

];
