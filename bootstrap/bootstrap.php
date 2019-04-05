<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use \cfg\Settings;

$config = Setup::createAnnotationMetadataConfiguration(Settings::$ormDomainObjectpath, Settings::$isDev = true);

$conn = Settings::$dataBaseData;

$entityManager = EntityManager::create($conn, $config);
